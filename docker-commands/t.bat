@echo off
cd %~dp0..
docker compose exec app1 php artisan test --filter=SystemDiagnosticsTest
