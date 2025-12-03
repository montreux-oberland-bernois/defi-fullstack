#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR=$(cd "$(dirname "$0")/.." && pwd)

check_file_exists() {
  local path="$1"
  if [ ! -f "$path" ]; then
    echo "MISSING: $path"
    return 1
  fi
  echo "FOUND: $path"
}

check_jwt=true
check_certs=true

print_usage(){
  echo "Usage: $0 [--no-jwt] [--no-certs]"
  echo "  --no-jwt    skip JWT key checks"
  echo "  --no-certs  skip cert checks"
  exit 1
}

while [ "$#" -gt 0 ]; do
  case "$1" in
    --no-jwt) check_jwt=false ; shift ;;
    --no-certs) check_certs=false ; shift ;;
    -h|--help) print_usage ;;
    *) echo "Unknown arg: $1"; print_usage ;;
  esac
done

echo "Verifying keys/certs in repository (flags: jwt=$check_jwt certs=$check_certs)"

JWT_PRIVATE="$ROOT_DIR/backend/config/jwt/private.pem"
JWT_PUBLIC="$ROOT_DIR/backend/config/jwt/public.pem"
CERT_PEM="$ROOT_DIR/certs/cert.pem"
CERT_KEY="$ROOT_DIR/certs/key.pem"

errs=0

if [ "$check_jwt" = true ]; then
  if ! check_file_exists "$JWT_PRIVATE"; then errs=$((errs+1)); fi
  if ! check_file_exists "$JWT_PUBLIC"; then errs=$((errs+1)); fi
fi

if [ "$check_certs" = true ]; then
  if ! check_file_exists "$CERT_PEM"; then errs=$((errs+1)); fi
  if ! check_file_exists "$CERT_KEY"; then errs=$((errs+1)); fi
fi

if [ $errs -ne 0 ]; then
  echo "One or more expected files are missing."
  exit 2
fi

if [ "$check_jwt" = true ]; then
  echo "Checking JWT private key is valid RSA private key..."
  if ! openssl pkey -in "$JWT_PRIVATE" -check -noout >/dev/null 2>&1; then
    echo "JWT private key is invalid"
    exit 3
  fi

  echo "Checking JWT public key..."
  if ! openssl pkey -pubin -in "$JWT_PUBLIC" -noout >/dev/null 2>&1; then
    if ! openssl rsa -in "$JWT_PRIVATE" -pubout -out /dev/null 2>&1; then
      echo "JWT public key failed validation"
      exit 4
    fi
  fi

  echo "Verify private key permissions (strict)"
  perms=$(stat -f '%A' "$JWT_PRIVATE" 2>/dev/null || stat -c '%a' "$JWT_PRIVATE" 2>/dev/null || echo "000")
  if [ "$perms" -gt 600 ]; then
    echo "Warning: JWT private key permissions are too open: $perms (expected 600 or stricter)"
  fi
fi

if [ "$check_certs" = true ]; then
  echo "Inspecting self-signed certificate..."
  if ! openssl x509 -in "$CERT_PEM" -noout >/dev/null 2>&1; then
    echo "Certificate is invalid or cannot be parsed"
    exit 5
  fi
fi

echo "All requested checks passed."
exit 0
