#include <Arduino.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <WiFiClientSecure.h>
#include <ArduinoJson.h>
#include <SPI.h>
#include <TFT_eSPI.h>
#include <qrcode.h>

// --- 1. KONFIGURASI WI-FI ---
const char* ssid     = "NANDIO";
const char* password = "15162424";

// --- 2. PIN & DELAY ---
const int RELAY_PIN     = 26;   // Pin Relay / Solenoid Valve Pompa
const int POLLING_DELAY = 2000; // Cek status bayar tiap 2 detik

// --- 3. URL ENDPOINT RAILWAY ---
const char* API_QRIS_INFO    = "https://smart-dispenser-app-production.up.railway.app/api/qris/info";
const char* API_CHECK_STATUS = "https://smart-dispenser-app-production.up.railway.app/api/qris/check-status";
const char* API_COMPLETE     = "https://smart-dispenser-app-production.up.railway.app/api/qris/complete";

TFT_eSPI tft = TFT_eSPI();
unsigned long lastPollTime = 0;

// Deklarasi Fungsi
void loadAndShowQRIS();
void renderQRToLCD(const char* qrData, const char* merchantName);
void checkPaymentStatus();
void dispenseWater(int txId);

void setup() {
  Serial.begin(115200);
  delay(1000);

  pinMode(RELAY_PIN, OUTPUT);
  digitalWrite(RELAY_PIN, LOW); // Relay Off di awal

  // Inisialisasi Layar LCD
  tft.init();
  tft.setRotation(1); // Mode Landscape
  tft.fillScreen(TFT_BLACK);
  tft.setTextColor(TFT_WHITE, TFT_BLACK);
  tft.setTextSize(2);
  tft.setCursor(10, 20);
  tft.print("Koneksi WiFi...");

  // Hubungkan ke Wi-Fi
  Serial.print("Menghubungkan ke Wi-Fi: ");
  Serial.println(ssid);
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("\nWi-Fi Terhubung!");
  tft.fillScreen(TFT_BLACK);
  tft.setCursor(10, 20);
  tft.print("Mengambil QRIS...");

  // Load QRIS dari API
  loadAndShowQRIS();
}

void loop() {
  // Cek pembayaran berkala tiap 2 detik (Polling)
  if (millis() - lastPollTime >= POLLING_DELAY) {
    lastPollTime = millis();
    checkPaymentStatus();
  }
}

// 1. Ambil String QRIS dari Railway
void loadAndShowQRIS() {
  if (WiFi.status() == WL_CONNECTED) {
    WiFiClientSecure client;
    client.setInsecure(); // Bypass verifikasi CA sertifikat SSL
    
    HTTPClient http;
    http.setTimeout(10000); // Timeout 10 detik

    if (http.begin(client, API_QRIS_INFO)) {
      // PERBAIKAN: Header wajib agar tidak di-block Railway (502 Bad Gateway)
      http.addHeader("User-Agent", "ESP32-SmartDispenser");
      http.addHeader("Accept", "application/json");

      int httpCode = http.GET();
      Serial.print("HTTP Code QRIS: ");
      Serial.println(httpCode);

      if (httpCode == HTTP_CODE_OK) {
        String payload = http.getString();
        DynamicJsonDocument doc(2048);
        DeserializationError error = deserializeJson(doc, payload);

        if (!error) {
          const char* qrString = doc["qr_string"] | "";
          const char* merchant = doc["merchant"] | "Smart Dispenser";

          if (strlen(qrString) > 0) {
            renderQRToLCD(qrString, merchant);
          } else {
            Serial.println("String QRIS kosong dalam JSON.");
            tft.fillScreen(TFT_BLACK);
            tft.setCursor(10, 20);
            tft.print("QRIS Kosong!");
          }
        } else {
          Serial.print("Gagal Parse JSON: ");
          Serial.println(error.c_str());
        }
      } else {
        Serial.print("Gagal mengambil QRIS, HTTP Respon: ");
        Serial.println(httpCode);
        tft.fillScreen(TFT_BLACK);
        tft.setCursor(10, 20);
        tft.printf("HTTP Err: %d", httpCode);
      }
      http.end();
    } else {
      Serial.println("Gagal terhubung ke server Railway (http.begin error)");
    }
  }
}

// 2. Gambar QR Code di LCD TFT
void renderQRToLCD(const char* qrData, const char* merchantName) {
  tft.fillScreen(TFT_WHITE);
  
  tft.setTextColor(TFT_BLACK, TFT_WHITE);
  tft.setTextSize(2);
  tft.setCursor(10, 5);
  tft.print(merchantName);

  QRCode qrcode;
  // PERBAIKAN: Gunakan Versi 8 (Kapasitas ~300 char) & Alokasi Buffer Dinamis
  uint8_t version = 8;
  uint16_t bufferSize = qrcode_getBufferSize(version);
  uint8_t* qrcodeData = (uint8_t*) malloc(bufferSize);

  if (qrcodeData == NULL) {
    Serial.println("Gagal alokasi RAM untuk QR Code!");
    return;
  }

  // Inisialisasi QR Code
  int8_t result = qrcode_initText(&qrcode, qrcodeData, version, 0, qrData);

  if (result == 0) {
    int scale = 3;
    int offsetX = (tft.width() - (qrcode.size * scale)) / 2; // Center horizontal
    int offsetY = 30;

    for (uint8_t y = 0; y < qrcode.size; y++) {
      for (uint8_t x = 0; x < qrcode.size; x++) {
        if (qrcode_getModule(&qrcode, x, y)) {
          tft.fillRect(offsetX + (x * scale), offsetY + (y * scale), scale, scale, TFT_BLACK);
        } else {
          tft.fillRect(offsetX + (x * scale), offsetY + (y * scale), scale, scale, TFT_WHITE);
        }
      }
    }

    tft.setCursor(10, tft.height() - 25);
    tft.setTextSize(2);
    tft.setTextColor(TFT_RED, TFT_WHITE);
    tft.print("TARIF: Rp 1.000");
  } else {
    Serial.println("String QR Terlalu Panjang untuk Versi ini!");
  }

  free(qrcodeData); // Bebaskan memori
}

// 3. Polling Cek Status Pembayaran
void checkPaymentStatus() {
  if (WiFi.status() == WL_CONNECTED) {
    WiFiClientSecure client;
    client.setInsecure();
    
    HTTPClient http;
    http.setTimeout(5000);

    if (http.begin(client, API_CHECK_STATUS)) {
      http.addHeader("User-Agent", "ESP32-SmartDispenser");
      http.addHeader("Accept", "application/json");

      int httpCode = http.GET();

      if (httpCode == HTTP_CODE_OK) {
        String payload = http.getString();
        DynamicJsonDocument doc(1024);
        deserializeJson(doc, payload);

        bool dispense = doc["dispense"] | false;
        int txId = doc["id"] | 0;

        if (dispense && txId > 0) {
          Serial.println("Pembayaran Terverifikasi! Menuang Air...");
          dispenseWater(txId);
        }
      }
      http.end();
    }
  }
}

// 4. Tuang Air via Relay & Selesaikan Transaksi
void dispenseWater(int txId) {
  tft.fillScreen(TFT_BLUE);
  tft.setTextColor(TFT_WHITE, TFT_BLUE);
  tft.setTextSize(2);
  tft.setCursor(30, 100);
  tft.print("MENUANG AIR...");

  // Nyalakan Relay Pompa
  digitalWrite(RELAY_PIN, HIGH);
  delay(5000); // Durasi penuangan
  digitalWrite(RELAY_PIN, LOW);

  // Selesaikan transaksi di server
  WiFiClientSecure client;
  client.setInsecure();
  HTTPClient http;

  if (http.begin(client, API_COMPLETE)) {
    http.addHeader("Content-Type", "application/json");
    http.addHeader("User-Agent", "ESP32-SmartDispenser");

    String jsonBody = "{\"id\":" + String(txId) + "}";
    http.POST(jsonBody);
    http.end();
  }

  // Kembalikan tampilan ke QRIS
  loadAndShowQRIS();
}