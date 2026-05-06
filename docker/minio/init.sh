#!/bin/sh
sleep 5
mc alias set local http://localhost:9000 minioadmin minioadmin
mc mb local/carnet-lecture --ignore-existing
mc policy set download local/carnet-lecture
echo "✅ Bucket 'carnet-lecture' prêt !"
