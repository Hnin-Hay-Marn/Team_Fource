 <!DOCTYPE html>
 <html lang="ja">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>top bar</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/topbar.css">
 </head>
 <body>
    <div class="top-actions">
  <div id="google_translate_element" class="google-translate"></div>
  <button type="button" class="topbar-button" onclick="goPage('dashboard.php')">← Back to Dashboard</button>
</div>
<script>
function goPage(page) {
  window.location.href = page;
}

function googleTranslateElementInit() {
  var pageLang = document.documentElement.lang || 'en';
  new google.translate.TranslateElement({
    pageLanguage: pageLang,
    includedLanguages: 'en,ja,my,ko,zh-CN,th,vi,id,ne',
    layout: google.translate.TranslateElement.InlineLayout.SIMPLE
  }, 'google_translate_element');
}
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
 </body>
 </html>
 
