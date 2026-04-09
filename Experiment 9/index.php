<?php
// No session, no history
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Calculator</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

/* 🎨 Cartoon Background */
body {
    background: linear-gradient(to right, #ffecd2, #fcb69f);
    font-family: 'Comic Sans MS', cursive;
}

/* 🧸 Calculator Card */
.calculator {
    max-width: 420px;
    margin: 50px auto;
    padding: 25px;
    border-radius: 30px;
    background: #fff8dc;
    box-shadow: 8px 8px 0px #ff9f43;
}

/* Display */
.display {
    height: 65px;
    font-size: 24px;
    text-align: right;
    border-radius: 20px;
    border: 3px solid #ff9f43;
    background: #fff;
}

/* 🫧 Buttons */
.btn-calc {
    width: 75px;
    height: 60px;
    border-radius: 20px;
    font-size: 18px;
    font-weight: bold;
    border: none;
    color: #333;
    transition: 0.2s;
    box-shadow: 4px 4px 0px #999;
}

/* 🎨 Fun colors */
.btn-light { background: #ffadad; }
.btn-warning { background: #ffd6a5; }
.btn-info { background: #9bf6ff; }
.btn-secondary { background: #caffbf; }
.btn-danger { background: #ff8fab; }
.btn-dark { background: #bdb2ff; }
.btn-success { background: #a0c4ff; }

/* ✨ Cartoon bounce effect */
.btn-calc:hover {
    transform: translateY(-5px) scale(1.1);
}

/* 📜 History Styles */
.history-container {
    max-width: 420px;
    margin: 20px auto;
    padding: 15px;
    border-radius: 20px;
    background: #f0f0f0;
    border: 3px solid #ff9f43;
    max-height: 200px;
    overflow-y: auto;
}

.history-title {
    font-weight: bold;
    color: #333;
    margin-bottom: 10px;
    font-size: 16px;
}

.history-item {
    background: white;
    padding: 8px 12px;
    margin: 5px 0;
    border-radius: 10px;
    border-left: 4px solid #ff9f43;
    cursor: pointer;
    transition: 0.2s;
}

.history-item:hover {
    background: #fff8dc;
    transform: translateX(5px);
}

.history-empty {
    color: #999;
    font-style: italic;
    text-align: center;
    padding: 20px;
}

</style>
</head>

<body>

<div class="calculator text-center">

<input type="text" id="display" class="form-control display mb-3" readonly>

<div class="d-flex flex-wrap gap-2 justify-content-center">

<?php
$nums = ['7','8','9','4','5','6','1','2','3','0','.'];
foreach ($nums as $n) {
    echo "<button class='btn btn-light btn-calc' onclick='press(\"$n\")'>$n</button>";
}
?>

<button class="btn btn-warning btn-calc" onclick="press('+')">+</button>
<button class="btn btn-warning btn-calc" onclick="press('-')">-</button>
<button class="btn btn-warning btn-calc" onclick="press('*')">*</button>
<button class="btn btn-warning btn-calc" onclick="press('/')">/</button>

<button class="btn btn-info btn-calc" onclick="press('Math.sin(')">sin</button>
<button class="btn btn-info btn-calc" onclick="press('Math.cos(')">cos</button>
<button class="btn btn-info btn-calc" onclick="press('Math.sqrt(')">√</button>

<button class="btn btn-secondary btn-calc" onclick="press('Math.PI')">π</button>
<button class="btn btn-secondary btn-calc" onclick="press('Math.E')">e</button>

<button class="btn btn-danger btn-calc" onclick="clearDisplay()">C</button>
<button class="btn btn-dark btn-calc" onclick="del()">⌫</button>
<button class="btn btn-success btn-calc" onclick="calculate()">=</button>
<button class="btn btn-warning btn-calc" onclick="clearHistory()" style="background: #ff6b6b !important;">🗑️</button>

</div>

</div>

<!-- 📜 History Container -->
<div class="history-container">
    <div class="history-title">📝 History</div>
    <div id="history" class="history-empty">No calculations yet</div>
</div>

<script>
let history = [];

// Load history from localStorage on page load
window.addEventListener('load', function() {
    const saved = localStorage.getItem('calcHistory');
    if (saved) {
        history = JSON.parse(saved);
        updateHistoryDisplay();
    }
});

function addToHistory(expression, result) {
    history.unshift(`${expression} = ${result}`);
    if (history.length > 15) history.pop(); // Keep only last 15 items
    localStorage.setItem('calcHistory', JSON.stringify(history));
    updateHistoryDisplay();
}

function updateHistoryDisplay() {
    const historyDiv = document.getElementById('history');
    if (history.length === 0) {
        historyDiv.innerHTML = '<div class="history-empty">No calculations yet</div>';
    } else {
        historyDiv.innerHTML = history.map((item, index) => 
            `<div class="history-item" onclick="recallHistory(this)">${item}</div>`
        ).join('');
    }
}

function recallHistory(element) {
    const text = element.textContent;
    const result = text.split(' = ')[1];
    document.getElementById("display").value = result;
}

function clearHistory() {
    if (confirm('Are you sure you want to clear history?')) {
        history = [];
        localStorage.removeItem('calcHistory');
        updateHistoryDisplay();
    }
}

function press(val) {
    document.getElementById("display").value += val;
}

function clearDisplay() {
    document.getElementById("display").value = "";
}

function del() {
    let d = document.getElementById("display");
    d.value = d.value.slice(0,-1);
}

function calculate() {
    let expr = document.getElementById("display").value;
    try {
        let result = eval(expr);
        document.getElementById("display").value = result;
        addToHistory(expr, result);
    } catch {
        document.getElementById("display").value = "Oops!";
    }
}
</script>

</body>
</html>