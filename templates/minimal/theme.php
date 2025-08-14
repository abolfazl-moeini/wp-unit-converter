<div class="container">
	<label for="category">Category:</label>
	<select id="category">
		<option value="">Select</option>
		<option value="length">Length</option>
		<option value="speed">Speed</option>
		<option value="temperature">Temperature</option>
		<option value="volume">Volume</option>
		<option value="weight">Weight</option>
	</select>

	<label for="fromUnit">From:</label>
	<select id="fromUnit"></select>

	<label for="toUnit">To:</label>
	<select id="toUnit"></select>

	<label for="value">Value:</label>
	<input type="number" id="value" placeholder="Enter value">

	<button id="convert">Convert</button>

	<div id="result"></div>
</div>
