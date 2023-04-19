#!/bin/bash

# to support CloudFront using this library:
# https://github.com/dreamonkey/laravel-cloudfront-url-signer/tree/v3.2.0
# it must use a local file pointing to the private key.
#
# when running on AWS ECS there is limited support to mounting files
# so this is a workaround to generate the file from an environment variable value
if [[ -n "$CLOUDFRONT_PRIVATE_KEY_PATH" ]]; then
  echo -e "$CLOUDFRONT_PRIVATE_KEY_VALUE" | tr -d '"' > $CLOUDFRONT_PRIVATE_KEY_PATH
  chown www-data:www-data $CLOUDFRONT_PRIVATE_KEY_PATH
  chmod 0600 $CLOUDFRONT_PRIVATE_KEY_PATH
fi

# run the original entrypoint of the PHP container
exec /usr/local/bin/docker-php-entrypoint "$@"
