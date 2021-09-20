## Retry with exponential backoff

Run server
```
php -S 0.0.0.0:8000 server.php
```

### Exponential backoff 

When we need to do the next retry, we have to increase the wait time to do the request, following this function: 

```
T(n) = base * pow(multiplier, n)
```
