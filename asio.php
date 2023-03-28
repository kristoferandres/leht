<!DOCTYPE html>
<html>
<head>
	<title>Change Text Example</title>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="row">
  <div class="col-md-6">
    <div class="input-group mb-3">
      <span class="input-group-text" id="row1">Row 1</span>
      <input type="text" class="form-control" placeholder="Type your text here" aria-label="Type your text here" aria-describedby="row1">
      <button class="btn btn-outline-secondary" type="button">Change</button>
    </div>
  </div>
  <div class="col-md-6">
    <div class="input-group mb-3">
      <span class="input-group-text" id="row2">Row 2</span>
      <input type="text" class="form-control" placeholder="Type your text here" aria-label="Type your text here" aria-describedby="row2">
      <button class="btn btn-outline-secondary" type="button">Change</button>
    </div>
  </div>
  <div class="col-md-6">
    <div class="input-group mb-3">
      <span class="input-group-text" id="row3">Row 3</span>
      <input type="text" class="form-control" placeholder="Type your text here" aria-label="Type your text here" aria-describedby="row3">
      <button class="btn btn-outline-secondary" type="button">Change</button>
    </div>
  </div>
  <div class="col-md-6">
    <div class="input-group mb-3">
      <span class="input-group-text" id="row4">Row 4</span>
      <input type="text" class="form-control" placeholder="Type your text here" aria-label="Type your text here" aria-describedby="row4">
      <button class="btn btn-outline-secondary" type="button">Change</button>
    </div>
  </div>
  <div class="col-md-6">
    <div class="input-group mb-3">
      <span class="input-group-text" id="row5">Row 5</span>
      <input type="text" class="form-control" placeholder="Type your text here" aria-label="Type your text here" aria-describedby="row5">
      <button class="btn btn-outline-secondary" type="button">Change</button>
    </div>
  </div>
</div>

	<script>
		function editRow(rowId) {
  // Get the row element
  var row = document.getElementById(rowId);

  // Hide the text and show the input box
  row.getElementsByTagName('p')[0].style.display = 'none';
  row.getElementsByTagName('input')[0].style.display = 'block';

  // Set the input box value to the current text
  var text = row.getElementsByTagName('p')[0].innerText;
  row.getElementsByTagName('input')[0].value = text;

  // Hide the Edit button and show the Save button
  row.getElementsByTagName('button')[0].style.display = 'none';
  row.getElementsByTagName('button')[1].style.display = 'block';
}

function saveRow(rowId) {
  // Get the row element
  var row = document.getElementById(rowId);

  // Get the value of the input box
  var newValue = row.getElementsByTagName('input')[0].value;

  // Set the new value to the text element
  row.getElementsByTagName('p')[0].innerText = newValue;

  // Hide the input box and show the text
  row.getElementsByTagName('p')[0].style.display = 'block';
  row.getElementsByTagName('input')[0].style.display = 'none';

  // Hide the Save button and show the Edit button
  row.getElementsByTagName('button')[0].style.display = 'block';
  row.getElementsByTagName('button')[1].style.display = 'none';
}
	</script>
</body>
</html>