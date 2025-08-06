<!-- Add this section where you want the quote generator to appear -->
<section class="quote-section" style="background: #91a0da; padding: 40px 0;">
  <div class="wrapper" style="width: 605px; margin: 0 auto; background: #fff; border-radius: 15px; padding: 30px 30px 25px;">
    <header class="main-header" style="font-size: 35px; font-weight: 600; text-align: center;">Quote of the Day</header>

    <div class="content" style="margin: 35px 0;">
      <div class="quote-area" style="display: flex; justify-content: center; align-items: center; font-size: 22px; text-align: center; word-break: break-word; position: relative;">
        <i class="fas fa-quote-left" style="font-size: 15px; color: #a9b2d4; margin: 3px 10px 0 0;"></i>
        <p class="quote" style="max-width: 500px; transition: opacity 0.3s ease;">In every step you take, thank God</p>
        <i class="fas fa-quote-right" style="font-size: 15px; color: #a9b2d4; display: flex; align-items: flex-end; margin: 0 0 3px 10px;"></i>
      </div>

      <div class="author" style="display: flex; font-size: 18px; font-style: italic; margin-top: 20px; justify-content: flex-end; color: #555;">
        <span>-</span>
        <span class="name" style="transition: opacity 0.3s ease;">Rebeca Paul</span>
      </div>
    </div>

    <div class="buttons" style="border-top: 1px solid #ccc; margin-top: 20px;">
      <div class="features" style="display: flex; align-items: center; justify-content: space-between; margin-top: 20px;">
        <ul style="display: flex; padding-left: 0;">
          <li class="sound" title="Listen" style="list-style: none; margin: 0 5px; height: 47px; width: 47px; display: flex; cursor: pointer; align-items: center; color: #5372F0; justify-content: center; border-radius: 50%; border: 2px solid #5372F0; transition: all 0.3s ease;"><i class="fas fa-volume-up"></i></li>
          <li class="copy" title="Copy" style="list-style: none; margin: 0 5px; height: 47px; width: 47px; display: flex; cursor: pointer; align-items: center; color: #5372F0; justify-content: center; border-radius: 50%; border: 2px solid #5372F0; transition: all 0.3s ease;"><i class="fas fa-copy"></i></li>
          <li class="twitter" title="Tweet" style="list-style: none; margin: 0 5px; height: 47px; width: 47px; display: flex; cursor: pointer; align-items: center; color: #5372F0; justify-content: center; border-radius: 50%; border: 2px solid #5372F0; transition: all 0.3s ease;"><i class="fab fa-twitter"></i></li>
        </ul>
        <button style="border: none; outline: none; color: #fff; cursor: pointer; font-size: 16px; padding: 13px 22px; border-radius: 30px; background: #5372F0; transition: opacity 0.3s ease;">New Quote</button>
      </div>
    </div>
  </div>
</section>

<!-- Add this script right before the closing </body> tag -->
<script>
// DOM Elements
const elements = {
  header: document.querySelector(".main-header"),
  quoteText: document.querySelector(".quote"),
  authorName: document.querySelector(".author .name"),
  quoteBtn: document.querySelector(".quote-section button"),
  soundBtn: document.querySelector(".sound"),
  copyBtn: document.querySelector(".copy"),
  twitterBtn: document.querySelector(".twitter")
};

// Header Options
const headerTitles = [
  "Quote of the Day",
  "Fresh Inspiration",
  "Wisdom for You", 
  "Words to Think About",
  "Daily Motivation"
];

// Fallback quotes when API fails
const fallbackQuotes = [
  {
    content: "The greatest glory in living lies not in never falling, but in rising every time we fall.",
    author: "Nelson Mandela"
  },
  {
    content: "The way to get started is to quit talking and begin doing.",
    author: "Walt Disney"
  },
  {
    content: "Your time is limited, so don't waste it living someone else's life.",
    author: "Steve Jobs"
  }
];

// Get random header title
function getRandomHeader() {
  return headerTitles[Math.floor(Math.random() * headerTitles.length)];
}

// Fade animation helper
async function fadeElement(element, opacity) {
  return new Promise(resolve => {
    element.style.transition = "opacity 0.3s ease";
    element.style.opacity = opacity;
    setTimeout(resolve, 300);
  });
}

// Get quote from API or fallback
async function fetchQuote() {
  try {
    const response = await fetch("https://api.quotable.io/random");
    if (!response.ok) throw new Error("API failed");
    return await response.json();
  } catch (error) {
    console.warn("Using fallback quotes:", error);
    return fallbackQuotes[Math.floor(Math.random() * fallbackQuotes.length)];
  }
}

// Update UI with new quote
async function updateQuote() {
  // Set loading state
  elements.quoteBtn.disabled = true;
  elements.quoteBtn.textContent = "Loading...";
  
  // Immediately update header
  elements.header.textContent = getRandomHeader();
  
  // Fade out current content
  await fadeElement(elements.quoteText, 0);
  await fadeElement(elements.authorName, 0);
  
  // Get new quote
  const quote = await fetchQuote();
  
  // Update content
  elements.quoteText.textContent = quote.content;
  elements.authorName.textContent = quote.author;
  
  // Fade in new content
  await fadeElement(elements.quoteText, 1);
  await fadeElement(elements.authorName, 1);
  
  // Reset button
  elements.quoteBtn.disabled = false;
  elements.quoteBtn.textContent = "New Quote";
}

// Text-to-speech function
function speakQuote() {
  const utterance = new SpeechSynthesisUtterance(
    `${elements.quoteText.textContent} by ${elements.authorName.textContent}`
  );
  speechSynthesis.speak(utterance);
}

// Copy quote to clipboard
function copyQuote() {
  navigator.clipboard.writeText(
    `${elements.quoteText.textContent} — ${elements.authorName.textContent}`
  );
  alert("Quote copied to clipboard!");
}

// Share on Twitter
function tweetQuote() {
  const tweetUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(
    `${elements.quoteText.textContent} — ${elements.authorName.textContent}`
  )}`;
  window.open(tweetUrl, "_blank");
}

// Event Listeners
elements.quoteBtn.addEventListener("click", updateQuote);
elements.soundBtn.addEventListener("click", speakQuote);
elements.copyBtn.addEventListener("click", copyQuote);
elements.twitterBtn.addEventListener("click", tweetQuote);

// Initialize on page load
window.addEventListener("DOMContentLoaded", updateQuote);
</script>