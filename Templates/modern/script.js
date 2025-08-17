document.addEventListener('DOMContentLoaded', () => {

    const API_URL = "/api/convert";

// --- DOM --------------------------------------------------------------------
    const $ = (sel, root = document) => root.querySelector(sel);
    const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
    const statusEl = $("#status");
    const resultEl = $("#result-value");
    const resultMetaEl = $("#result-meta");
    const valueEl = $("#value");
    const fromEl = $("#from");
    const toEl = $("#to");
    const btnConvert = $("#convert");
    const btnReset = $("#reset");
    const spinner = btnConvert.querySelector('.spinner');

    let currentCategory = 'length';

// --- Helpers ----------------------------------------------------------------
    function setStatus(text) {
        statusEl.textContent = text;
    }

    function setLoading(is) {
        spinner.hidden = !is;
        btnConvert.disabled = is;
    }

    function now() {
        return new Date().toLocaleString();
    }

    function debounce(fn, ms) {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn(...args), ms);
        }
    }

    function fillUnits(cat) {
        const units = WP_Unit_Converter.categories[cat]&&WP_Unit_Converter.categories[cat].units || [];
        fromEl.innerHTML = units.map(u => `<option value="${u}">${u.toUpperCase()}</option>`).join('');
        toEl.innerHTML = units.map(u => `<option value="${u}">${u.toUpperCase()}</option>`).join('');
        if (units.length > 1) {
            toEl.selectedIndex = 1;
        }
    }

    function swapUnits() {
        const a = fromEl.value, b = toEl.value;
        fromEl.value = b;
        toEl.value = a;
        convert();
    }

    function validate() {
        const v = valueEl.value.trim();
        const valid = v !== '' && !Number.isNaN(Number(v));
        btnConvert.disabled = !valid;
        return valid;
    }

    async function convert() {
        if (!validate()) {
            resultEl.textContent = '–';
            resultMetaEl.textContent = 'Enter a valid number';
            return;
        }
        const payload = {
            category: currentCategory,
            fromUnit: fromEl.value,
            toUnit: toEl.value,
            value: Number(valueEl.value)
        };

        setLoading(true);
        setStatus('Contacting server…');

        // Abort stale requests when user types fast
        convert.controller?.abort();
        const controller = new AbortController();
        convert.controller = controller;

        try {
            const res = await fetch(API_URL, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload),
                signal: controller.signal
            });
            if (!res.ok) {
                const text = await res.text().catch(() => res.statusText);
                throw new Error(text || `HTTP ${res.status}`);
            }
            const data = await res.json();
            // Expected server response shape:
            // { result: number, unit?: string, precision?: number, meta?: string }
            const unit = data.unit || payload.toUnit.toUpperCase();
            const precision = Number.isFinite(data.precision) ? data.precision : 6;
            const value = Number(data.result);
            const pretty = Number.isFinite(value) ? value.toFixed(Math.min(precision, 12)).replace(/\.0+$/, '').replace(/(\.\d*?)0+$/, '$1') : 'NaN';
            resultEl.textContent = `${pretty} ${unit}`;
            resultMetaEl.textContent = data.meta || `${payload.value} ${payload.fromUnit.toUpperCase()} → ${unit}`;
            setStatus('Done');
            $("#last-updated").textContent = `Updated ${now()}`;
        } catch (err) {
            if (err.name === 'AbortError') {
                return;
            }
            resultEl.textContent = 'Error';
            resultMetaEl.innerHTML = `<span class="err">${(err && err.message) || 'Request failed'}</span>`;
            setStatus('Failed');
        } finally {
            setLoading(false);
        }
    }

    function setCategory(cat) {
        currentCategory = cat;
        fillUnits(cat);
        // Update chips states
        $$('.chip').forEach(c => {
            const on = c.dataset.cat === cat;
            c.setAttribute('aria-pressed', on);
            c.setAttribute('aria-selected', on);
        });
        setStatus(`Category: ${cat}`);
        resultEl.textContent = '–';
        resultMetaEl.textContent = 'No conversion yet';
    }

// --- Events -----------------------------------------------------------------
    $$('.chip').forEach(chip => chip.addEventListener('click', () => setCategory(chip.dataset.cat)));
    $('#swap').addEventListener('click', swapUnits);
    btnConvert.addEventListener('click', convert);
    btnReset.addEventListener('click', () => {
        valueEl.value = '';
        validate();
        resultEl.textContent = '–';
        resultMetaEl.textContent = 'No conversion yet';
        valueEl.focus();
    });
    valueEl.addEventListener('input', debounce(() => {
        if (validate()) convert();
    }, 400));
    fromEl.addEventListener('change', () => validate() && convert());
    toEl.addEventListener('change', () => validate() && convert());

// --- Init -------------------------------------------------------------------
    setCategory(currentCategory);
    validate();
});
