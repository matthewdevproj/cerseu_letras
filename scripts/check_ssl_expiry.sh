#!/bin/bash
EXPIRY_DATE=$(openssl x509 -in /home/posgrado/PosgradoLetras8/docker/nginx/ssl/fullchain.pem -noout -enddate | cut -d= -f2)
EXPIRY_EPOCH=$(date -d "$EXPIRY_DATE" +%s)
CURRENT_EPOCH=$(date +%s)
DAYS_LEFT=$(( (EXPIRY_EPOCH - CURRENT_EPOCH) / 86400 ))

echo "📅 Certificado SSL Let's Encrypt"
echo "   Dominio:    posgrado.letras.unmsm.edu.pe"
echo "   Válido hasta: $EXPIRY_DATE"
echo "   Días restantes: $DAYS_LEFT días"

if [ $DAYS_LEFT -lt 30 ]; then
    echo "⚠️  ADVERTENCIA: Certificado expira en menos de 30 días"
    echo "   Ejecuta: /home/posgrado/renew_ssl.sh"
fi
