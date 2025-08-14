<div class="app card">
	<header>
		<div class="brand" aria-label="Unit Converter">
			<!-- Inline SVG logo -->
			<svg viewBox="0 0 48 48" aria-hidden="true" focusable="false">
				<defs>
					<linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
						<stop offset="0" stop-color="var(--accent)"/>
						<stop offset="1" stop-color="var(--accent-2)"/>
					</linearGradient>
				</defs>
				<rect x="2" y="2" width="44" height="44" rx="10" fill="url(#g)" opacity=".18"/>
				<path d="M10 30h28" stroke="url(#g)" stroke-width="3" stroke-linecap="round"/>
				<path d="M10 18h20" stroke="url(#g)" stroke-width="3" stroke-linecap="round"/>
				<circle cx="38" cy="18" r="3" fill="url(#g)"/>
			</svg>
			<span>Unit Converter</span>
		</div>
		<div class="muted" id="status">Ready</div>
	</header>

	<!-- Categories -->
	<section class="categories" role="tablist" aria-label="Unit categories">
		<button class="chip" role="tab" aria-selected="true" aria-pressed="true" data-cat="length">Length</button>
		<button class="chip" role="tab" aria-selected="false" aria-pressed="false" data-cat="speed">Speed</button>
		<button class="chip" role="tab" aria-selected="false" aria-pressed="false" data-cat="temperature">Temperature</button>
		<button class="chip" role="tab" aria-selected="false" aria-pressed="false" data-cat="volume">Volume</button>
		<button class="chip" role="tab" aria-selected="false" aria-pressed="false" data-cat="weight">Weight</button>
	</section>

	<!-- Converter grid -->
	<section class="grid" aria-labelledby="converter-label">
		<h2 id="converter-label" class="sr-only">Converter</h2>

		<div class="from">
			<label for="value">Value</label>
			<div class="field">
				<input id="value" name="value" type="number" step="any" inputmode="decimal" placeholder="Enter value" aria-describedby="value-help" />
			</div>
			<small id="value-help" class="muted">Type a number and choose units</small>
		</div>

		<div class="middle" aria-hidden="true">
			<button id="swap" class="btn swap" title="Swap units" type="button" aria-label="Swap units">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<polyline points="17 1 21 5 17 9"></polyline>
					<path d="M3 5h18a4 4 0 0 1 4 4v0"></path>
					<polyline points="7 23 3 19 7 15"></polyline>
					<path d="M21 19H3a4 4 0 0 1-4-4v0"></path>
				</svg>
			</button>
		</div>

		<div class="to">
			<label for="from">From</label>
			<div class="field">
				<select id="from" name="from"></select>
			</div>
		</div>

		<div class="from">
			<label for="to">To</label>
			<div class="field">
				<select id="to" name="to"></select>
			</div>
		</div>

		<div class="to actions">
			<button id="convert" class="btn" type="button">
				<svg class="spinner" viewBox="0 0 24 24" aria-hidden="true" hidden>
					<circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-dasharray="50 100"/>
				</svg>
				<span>Convert</span>
			</button>
			<button id="reset" class="btn outline" type="button">Clear</button>
		</div>
	</section>

	<output class="result" id="output" aria-live="polite" aria-atomic="true">
		<div>
			<div class="meta">Result</div>
			<div class="value" id="result-value">–</div>
		</div>
		<div class="meta" id="result-meta">No conversion yet</div>
	</output>

</div>
