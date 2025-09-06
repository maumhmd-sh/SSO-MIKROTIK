document.addEventListener('DOMContentLoaded', function() {
    var dateElement = document.getElementById('current-date');
    var now = new Date();
    var options = { year: 'numeric', month: 'long', day: 'numeric' };
    var formattedDate = now.toLocaleDateString('en-US', options);
    dateElement.textContent = formattedDate;
  });

 // Function to create a log entry
 const logEntry = (timestamp, message, type) => {
  const entryElement = document.createElement('div');
  entryElement.classList.add('log-entry', type);

  const timestampElement = document.createElement('span');
  timestampElement.classList.add('timestamp');
  timestampElement.textContent = `[${timestamp}]`;

  const messageElement = document.createElement('span');
  messageElement.classList.add('message');
  messageElement.textContent = message;

  entryElement.appendChild(timestampElement);
  entryElement.appendChild(messageElement);

  const logContainer = document.getElementById('log-container');
  logContainer.appendChild(entryElement);
};

// Function to fetch log entries from server
const fetchLogs = async () => {
  try {
    const response = await fetch('fetch_logs.php');
    const logs = await response.json();

    document.getElementById('log-container').innerHTML = '';

    logs.forEach(log => {
      logEntry(log.timestamp, log.message, log.type);
    });
  } catch (error) {
    console.error('Error fetching logs:', error);
  }
};

// Fetch logs every 5 seconds
setInterval(fetchLogs, 5000);
fetchLogs();