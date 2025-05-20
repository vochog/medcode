chrome.action.onClicked.addListener(() => {
  chrome.tabs.query({}, function(tabs) {
    tabs.forEach(tab => {
      if (tab.url && tab.url.toLowerCase().endsWith(".pdf")) {
        // Extract file name from URL
        const filename = tab.url.split('/').pop().split('#')[0].split('?')[0];

        // Send to local Python server
        fetch('http://localhost:5000/pdf', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ url: tab.url, filename: filename })
        }).catch(err => console.error("Error sending PDF info:", err));
      }
    });
  });
});