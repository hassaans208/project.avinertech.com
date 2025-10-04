I have written a new module in spring boot payments.avinertech.com

Only phase one has been completed yet

My target with integration is as follows

Once the application is complete (before deploye,emt) send all te data in json_encoded form to the following api

curl --location 'https://signal.avinertech.com/api/encrypt' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--data '{
    "value": "{\"tenantId\":\"tenant-123\",\"providerCode\":\"payoneer\",\"providerAccountId\":\"550e8400-e29b-41d4-a716-446655440000\",\"amountMinor\":39999,\"currency\":\"USD\",\"method\":\"credit_card\",\"orderId\":\"TEST-ORDER-123\",\"productName\":\"Premium Software License\",\"serviceName\":\"Enterprise Support Package\",\"companyName\":\"AvinerTech Solutions\",\"serviceHost\":\"https://example.com\",\"clientDomain\":\"client.example.com\",\"clientId\":\"client123\",\"clientReferenceId\":\"REF-789\",\"callbackUrl\":\"https://webhook.site/test\",\"metadata\":{\"customerId\":\"cust_123\",\"subscriptionId\":\"sub_456\"}}"
}'

Response
```
{
    "success": true,
    "original": "{\"tenantId\":\"tenant-123\",\"providerCode\":\"payoneer\",\"providerAccountId\":\"550e8400-e29b-41d4-a716-446655440000\",\"amountMinor\":39999,\"currency\":\"USD\",\"method\":\"credit_card\",\"orderId\":\"TEST-ORDER-123\",\"productName\":\"Premium Software License\",\"serviceName\":\"Enterprise Support Package\",\"companyName\":\"AvinerTech Solutions\",\"serviceHost\":\"https://example.com\",\"clientDomain\":\"client.example.com\",\"clientId\":\"client123\",\"clientReferenceId\":\"REF-789\",\"callbackUrl\":\"https://webhook.site/test\",\"metadata\":{\"customerId\":\"cust_123\",\"subscriptionId\":\"sub_456\"}}",
    "encrypted": "da865d9659d18e771a2330f73e4b5932971e296e69874ec8d12de5fc4930f4c72ff0b735fdaba23438a1cf2739d22fe3d65677310de82178d94f999fc3b8d16c536f6c6d10aff8f2666c8752cf5a44dd7099bfa2970c09333effb1d0d8204075eac8b424c6b585712206f953440ce58ba499561bca064b0029133a9d6e283c02a731c0b8ad454d828f28dc0db88c628828aad92eaa8ae2dd2b91c43799839f2b7b97438b4e9f145045d5582fbea1b77caf1e0bc3cb5bd68d4f3bd58674bfdf49d30b277f858debadaa8d983f2820f03357ed3dcbc8eff936bb8f6ffe9b2ac7f47cb49c568c57561268a62fada7bebc0799a37dbe3002dba85b955dc6af8daec7f74d35c8b0a0bb60ebd4b2d45151dee487ad00183e7c0a5e743eff80b413b3cc5ad21de5f6dbb2c7b65a10618ed585f2bf843f87ca5313f080a6ff1d58e694a3e61be5a88c4b3d16b5bb0c85399f4f45625f9639c1844ef4e3420cc1e78e6215db5f359266c4be6c53f606e6f208bb758ca93d732f5bc8f4e1f31b4304d12ba5c6dacb30a70db17a9d3b5a32d822a9888054712e17fbeb28ae2d65fa7eefb8d6b291c1172b4a6e2197ea44352ae9044cc1545e4627b1b60096989c58bd0e695e6d305b2e413230f677024dc03cfbc0f3260151ca2189751d994c69f6e5e7238cfb69ba3414ebb354e7347a5a3dbc1aa12eaf500aab935ecb8b9f9a0bae0ed7018cbaa3a9c715f1cf775a9a9e810c0a15deb4f4e6526f1f1ce77501ea2ac268af083fb81666d277d7f4bd1ac1943f5fc1557acd032061f5a9cbba3e92a88027bc"
}
```

take encrypted value from above and hit another api
curl --location 'https://payments.avinertech.com/v1/payments' \
--header 'Content-Type: application/json' \
--header 'X-APP-SIGNATURE: test-signature' \
--header 'X-ENC-SUB: test-subdomain' \
--data '{
  "signature": "test-signature-12345-encrypted-data"
}'

Response will be as follows
```
{
    "id": "655c3a39-cabb-45db-90ee-77b164a4ec0d",
    "tenantId": "tenant-123",
    "providerCode": "payoneer",
    "providerAccountId": "550e8400-e29b-41d4-a716-446655440000",
    "providerPaymentId": "PAY_1759603709215_F0228DAA",
    "status": "INITIATED",
    "amountMinor": 39999,
    "currency": "USD",
    "method": "credit_card",
    "orderId": "TEST-ORDER-123",
    "productName": "Premium Software License",
    "serviceName": "Enterprise Support Package",
    "companyName": "AvinerTech Solutions",
    "serviceHost": "https://example.com",
    "clientDomain": "client.example.com",
    "clientId": "client123",
    "clientReferenceId": "REF-789",
    "callbackUrl": "https://webhook.site/test",
    "checkoutUrl": "https://payments.avinertech.com/forms/payment/checkout?paymentId=655c3a39-cabb-45db-90ee-77b164a4ec0d",
    "metadata": {
        "customerId": "cust_123",
        "createdAt": "2025-10-04T18:48:29.220258273Z",
        "callbackUrl": "https://webhook.site/test",
        "subscriptionId": "sub_456",
        "serviceName": "Enterprise Support Package",
        "companyName": "AvinerTech Solutions"
    },
    "createdAt": "2025-10-04T18:48:29.256021Z",
    "updatedAt": "2025-10-04T18:48:29.256079Z",
    "error": null
}
```

open the checkoutUrl in a modal iframe screen
and wait for the thankyou page to appear in modal

Once tankyou page appears, close the modal and start deployement

After the step which says start deployement it should say Pay Now and also with a text 30 days free trial
