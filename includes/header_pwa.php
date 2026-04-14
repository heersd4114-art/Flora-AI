<link rel="manifest" href="manifest.json">
<meta name="theme-color" content="#22c55e">
<link rel="apple-touch-icon" href="assests/images/logo.png">

<script>
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      navigator.serviceWorker.register('sw.js')
        .then((registration) => {
          console.log('ServiceWorker registration successful with scope: ', registration.scope);
        }, (err) => {
          console.log('ServiceWorker registration failed: ', err);
        });
    });
  }
</script>
