#!/bin/bash

if [ $# -ne 1 ]; then
    echo "Usage: $0 <username>"
    exit 1
fi

ssh tecweb "rm -rf public_html src"

sftp $1@tecweb <<EOF
put -r "./public_html" "public_html"
put -r "./src" "src"
bye
EOF

if [ $? -eq 0 ]; then
    echo "Files successfully uploaded via SFTP."
else
    echo "SFTP upload failed."
    exit 1
fi
