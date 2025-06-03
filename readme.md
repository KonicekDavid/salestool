# REST API - správa kalkulací

Toto je ukázková implementace jednoduchého REST API v PHP 8.2 postaveného na frameworku **Nette**.

## ✅ Funkce

- **Vytvoření kalkulace**
- **Úprava stavu kalkulace**
- **Výpis kalkulace/kalkulací**

## 🛠 Požadavky

- Docker a Docker Compose

## 🚀 Spuštění projektu

1. Naklonujte repozitář:
```
git clone https://github.com/KonicekDavid/salestool.git
cd salestool
```
2. Spusťte Docker:
```
docker-compose up --build
```
3. Aplikace bude vy výchozím stavu dostupná na: http://localhost:8080

## 🗃 Databáze
Používá se SQLite. Po spuštění kontejneru se vytvoří databázový soubor db/database.sqlite.

## 📍 Endpointy
```
POST /api/v1/calculations      - vytvoření kalkulace - povinné údaje - jméno zákazníka, název tarifu, cena, měna
PUT  /api/v1/calculations/{id} - upravuje status konkrétní kalkulace - povinný údaj status
GET  /api/v1/calculations      - vrací seznam kalkulací - volitelné parametry page a limit
GET  /api/v1/calculations/{id} - vrací konkrétní kalkulaci
```

## 📚 Příklady volání API
**1. Vytvoření kalkulace**

Všechny níže uvedené parametry jsou povinné. Pozor, price musí být datový typ **float**.
```
POST /api/v1/calculations 
Content-Type: application/json

{
  "customer_name": "Miroslav Laciný",
  "tariff_name": "Na míru pro Míru",
  "price": 300.00,
  "currency": "CZK"
}
```
Vrací odpověď s HTTP kódem 201:
```
{
    "id": 1,
    "customerName": "Miroslav Laciný",
    "tariffName": "Na míru pro Míru",
    "price": 300.00,
    "currency": "CZK",
    "status": "new",
    "createdAt": "03.06.2025 00:00:01",
    "lastUpdate": "03.06.2025 00:00:01",
    "validity": "valid"
}
```
**2. Úprava stavu kalkulace**
```
POST /api/v1/calculations/1
Content-Type: application/json

{
  "status": "pending"
}
```
Vrací odpověď s HTTP kódem 200:
```
{
    "id": 1,
    "customerName": "Miroslav Laciný",
    "tariffName": "Na míru pro Míru",
    "price": 300.00,
    "currency": "CZK",
    "status": "pending",
    "createdAt": "03.06.2025 00:00:01",
    "lastUpdate": "03.06.2025 00:00:01",
    "validity": "valid"
}
```
**3. Výpis kalkulací**
```
GET /api/v1/calculations
Volitelně: 
GET /api/v1/calculations?page=1&limit=10
```
Vrací odpověď s HTTP kódem 200:
```
{
    "data": [{
        "id": 1,
        "customerName": "Miroslav Laciný",
        "tariffName": "Na míru pro Míru",
        "price": 300.00,
        "currency": "CZK",
        "status": "pending",
        "createdAt": "03.06.2025 00:00:01",
        "lastUpdate": "03.06.2025 00:00:01",
        "validity": "valid"
        },...
    ],
    "pagination": {
        "page": 1,
        "limit": 10,
        "totalPages": 2,
        "totalItems": 12
    }
}
```


**4. Výpis konkrétní kalkulace**
```
GET /api/v1/calculations/1
```
Vrací odpověď s HTTP kódem 200:
```
{
    "id": 1,
    "customerName": "Miroslav Laciný",
    "tariffName": "Na míru pro Míru",
    "price": 300.00,
    "currency": "CZK",
    "status": "pending",
    "createdAt": "03.06.2025 00:00:01",
    "lastUpdate": "03.06.2025 00:00:01",
    "validity": "valid"
}

```