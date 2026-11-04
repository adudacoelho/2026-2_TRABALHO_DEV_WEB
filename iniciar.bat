@echo off
chcp 65001 >nul
echo === Mercadinho Autonomo - iniciando ===
if not exist .env copy env .env >nul
php spark migrate
php spark db:seed DatabaseSeeder
echo.
echo Abra no navegador: http://localhost:8080/categorias  (Ctrl+C para parar)
start http://localhost:8080/categorias
php spark serve --port 8080
