// Tab switching functionality
document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('btnGenerate').addEventListener('click', function() {
    document.getElementById('generateForm').style.display = 'block';
    document.getElementById('getForm').style.display = 'none';
    this.classList.add('active');
    document.getElementById('btnGet').classList.remove('active');
  });
  document.getElementById('btnGet').addEventListener('click', function() {
    document.getElementById('generateForm').style.display = 'none';
    document.getElementById('getForm').style.display = 'block';
    this.classList.add('active');
    document.getElementById('btnGenerate').classList.remove('active');
  });
});

// Copy retrieved text functionality
function copyText() {
  const textToCopy = document.getElementById('textToCopy');
  if (!textToCopy) {
    showMessage('Text element not found', 'error');
    return;
  }
  
  const text = textToCopy.value;
  if (!text) {
    showMessage('No text to copy', 'error');
    return;
  }
  
  navigator.clipboard.writeText(text).then(() => {
    showMessage('Text copied to clipboard!', 'success');
  }).catch(err => {
    console.error('Copy failed:', err);
    showMessage('Failed to copy text. Please try again.', 'error');
  });
}

// Copy the generated link functionality
function copyLinkText() {
  var linkInput = document.getElementById("copyLink");
  linkInput.select();
  linkInput.setSelectionRange(0, 99999); // For mobile devices
  document.execCommand("copy");
  showInlineMessage("Link copied to clipboard!", "success");
}

// Download text as file functionality
function downloadText() {
  const textToCopy = document.getElementById('textToCopy');
  if (!textToCopy) {
    showMessage('Text element not found', 'error');
    return;
  }
  
  const text = textToCopy.value;
  if (!text) {
    showMessage('No text to download', 'error');
    return;
  }
  
  const blob = new Blob([text], { type: 'text/plain' });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = 'copied-text.txt';
  document.body.appendChild(a);
  a.click();
  window.URL.revokeObjectURL(url);
  document.body.removeChild(a);
  
  showMessage('Text downloaded successfully!', 'success');
}

// Function to show inline alert messages.
function showInlineMessage(message, type) {
  var msgEl = document.createElement("div");
  msgEl.className = "alert " + type;
  msgEl.innerText = message;
  var container = document.querySelector(".container");
  container.insertBefore(msgEl, container.firstChild);
  setTimeout(function() {
    if (msgEl) {
      msgEl.remove();
    }
  }, 3000);
}

// FAQ accordion toggle functionality.
document.addEventListener('DOMContentLoaded', function() {
  var faqItems = document.querySelectorAll('.faq-item');
  
  // Ensure all FAQs are closed by default
  faqItems.forEach(function(item) {
    item.classList.remove('active');
  });
  
  // Add click event listeners
  faqItems.forEach(function(item) {
    var question = item.querySelector('.faq-question');
    question.addEventListener('click', function() {
      // Close all other FAQs
      faqItems.forEach(function(otherItem) {
        if (otherItem !== item) {
          otherItem.classList.remove('active');
        }
      });
      
      // Toggle current FAQ
      item.classList.toggle('active');
    });
  });
});

// Update line numbers for text display
function updateLineNumbers() {
  var textElement = document.getElementById("textToCopy");
  var lineNumbersElement = document.querySelector(".line-numbers");
  var text = textElement.textContent.trim();
  var lines = text.split('\n');
  
  // Clear existing line numbers
  lineNumbersElement.innerHTML = '';
  
  // Add line numbers
  for (var i = 1; i <= lines.length; i++) {
    var lineNumber = document.createElement('div');
    lineNumber.textContent = i;
    lineNumbersElement.appendChild(lineNumber);
  }
}

// Initialize line numbers when text is displayed
document.addEventListener('DOMContentLoaded', function() {
  if (document.getElementById("textToCopy")) {
    updateLineNumbers();
  }
});

// Function to show message
function showMessage(message, type = 'success') {
    // Remove any existing messages
    const existingMessages = document.querySelectorAll('.message');
    existingMessages.forEach(msg => msg.remove());
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;
    
    document.body.appendChild(messageDiv);
    
    // Add slide-out animation before removing
    setTimeout(() => {
        messageDiv.style.animation = 'slideOut 0.3s ease-out forwards';
        setTimeout(() => {
            messageDiv.remove();
        }, 300);
    }, 2700);
}

// Initialize textarea height based on content
document.addEventListener('DOMContentLoaded', function() {
  const textarea = document.getElementById('textToCopy');
  if (textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = (textarea.scrollHeight) + 'px';
  }
});