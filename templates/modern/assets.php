<style>
    :root{
        --bg: #0b0f14;
        --panel: #121820;
        --muted: #5b6b7a;
        --text: #e9f0f6;
        --accent: #6aa9ff;
        --accent-2: #7ef0d6;
        --danger: #ff6b6b;
        --ring: rgba(106,169,255,.35);
        --radius: 16px;
        --gap: 14px;
    }
    @media (prefers-color-scheme: light){
        :root{ --bg:#f6f7fb; --panel:#ffffff; --text:#0b0f14; --muted:#5a6876; --ring: rgba(40,120,255,.25); }
    }
    *{ box-sizing: border-box }
    html,body{ height:100% }
    body{
        margin:0; font: 16px/1.45 system-ui, -apple-system, Segoe UI, Roboto, Inter, "Helvetica Neue", Arial;
        color:var(--text); background: radial-gradient(1200px 600px at 10% -10%, #142033 0%, transparent 50%),
    radial-gradient(1200px 600px at 110% 0%, #122b25 0%, transparent 50%), var(--bg);
        display:grid; place-items:center; padding:24px;
    }
    .app{
        width:min(960px, 100%); display:grid; gap:var(--gap);
    }
    .card{
        background: linear-gradient(180deg, rgba(255,255,255,.03), rgba(255,255,255,.02));
        border: 1px solid rgba(255,255,255,.06);
        border-radius: var(--radius);
        box-shadow: 0 10px 30px rgba(0,0,0,.25);
        backdrop-filter: blur(8px);
        padding: clamp(16px, 2.8vw, 24px);
    }
    header{ display:flex; align-items:center; justify-content:space-between; gap:8px; }
    .brand{ display:flex; align-items:center; gap:10px; font-weight:700; letter-spacing:.2px }
    .brand svg{ width:28px; height:28px }
    .muted{ color: var(--muted) }

    .categories{ display:grid; grid-template-columns: repeat(5, minmax(0,1fr)); gap:10px }
    .chip{ cursor:pointer; border-radius: 999px; padding:10px 12px; text-align:center; border:1px solid rgba(255,255,255,.08);
        background: linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02));
        user-select:none; display:flex; gap:8px; align-items:center; justify-content:center;
        transition: transform .08s ease, border-color .2s ease, background .2s ease;
    }
    .chip:hover{ transform: translateY(-1px) }
    .chip[aria-pressed="true"]{ outline: none; border-color: var(--accent); box-shadow: 0 0 0 6px var(--ring); }

    .grid{ display:grid; gap: var(--gap); grid-template-columns: repeat(12, 1fr) }
    .from, .to{ grid-column: span 5 }
    .middle{ grid-column: span 2; display:flex; align-items:end; justify-content:center }
    @media (max-width: 720px){ .from,.to{ grid-column: span 12 } .middle{ grid-column: span 12 } }

    label{ font-size:.9rem; color: var(--muted) }
    .field{ display:flex; gap:8px; width:100% }
    input[type="number"], select{
        width:100%; padding:12px 14px; border-radius:12px; border:1px solid rgba(255,255,255,.08);
        background: var(--panel); color: var(--text);
        outline:none; transition: box-shadow .2s ease, border-color .2s ease;
    }
    input[type="number"]:focus, select:focus{ border-color: var(--accent); box-shadow: 0 0 0 6px var(--ring) }

    .btn{ cursor:pointer; padding:12px 14px; border-radius: 12px; border:1px solid rgba(255,255,255,.1);
        background: linear-gradient(180deg, rgba(106,169,255,.15), rgba(106,169,255,.08));
        color:var(--text); font-weight:600; display:inline-flex; align-items:center; justify-content:center; gap:8px;
        transition: transform .08s ease, box-shadow .2s ease, border-color .2s ease;
    }
    .btn:hover{ transform: translateY(-1px) }
    .btn:disabled{ opacity:.55; cursor:not-allowed }
    .btn.outline{ background: transparent }

    .swap{ border-radius: 999px; padding:10px 12px; border:1px dashed rgba(255,255,255,.2); background:transparent }
    .actions{ display:flex; gap:10px; align-items:end; justify-content:space-between; flex-wrap:wrap }

    .result{
        display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 16px;
        border-radius: 12px; border:1px solid rgba(255,255,255,.08); background: linear-gradient(180deg, rgba(255,255,255,.03), rgba(255,255,255,.02));
    }
    .result .value{ font-size: clamp(22px, 3.2vw, 28px); font-weight: 800; letter-spacing:.3px }
    .result .meta{ font-size:.9rem; color: var(--muted) }

    .err{ color: var(--danger) }
    .sr-only{ position:absolute; left:-10000px; width:1px; height:1px; overflow:hidden }

    .footer{ display:flex; gap:12px; align-items:center; justify-content:space-between; color:var(--muted); font-size:.9rem }
    .spinner{ width:18px; height:18px; display:inline-block }
    .spinner circle{ transform-origin: center; animation: spin 1s linear infinite }
    @keyframes spin { from{ transform: rotate(0) } to{ transform: rotate(360deg) } }
</style>

<script>
    // --- Config -----------------------------------------------------------------
    // Backend endpoint. Change this to your server path. Supports relative path.
    const API_URL = "/api/convert";

    // Available units per category (for dropdowns only)
    // Conversion is NOT done on client; server performs it.
    const UNIT_SETS = {
        length: ["m","cm","mm","km","inch","foot","yard","mile"],
        speed: ["m/s","km/h","mph","knot"],
        temperature: ["c","f","k"],
        volume: ["l","ml","m3","ft3","gal"],
        weight: ["kg","g","mg","lb","oz","ton"]
    };

    // --- DOM --------------------------------------------------------------------
    const $ = (sel, root=document) => root.querySelector(sel);
    const $$ = (sel, root=document) => Array.from(root.querySelectorAll(sel));
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
    function setStatus(text){ statusEl.textContent = text; }
    function setLoading(is){ spinner.hidden = !is; btnConvert.disabled = is; }
    function now(){ return new Date().toLocaleString(); }
    function debounce(fn, ms){ let t; return (...args)=>{ clearTimeout(t); t = setTimeout(()=>fn(...args), ms); } }
    function fillUnits(cat){
        const units = UNIT_SETS[cat] || [];
        fromEl.innerHTML = units.map(u=>`<option value="${u}">${u.toUpperCase()}</option>`).join('');
        toEl.innerHTML = units.map(u=>`<option value="${u}">${u.toUpperCase()}</option>`).join('');
        if(units.length>1){ toEl.selectedIndex = 1; }
    }

    function swapUnits(){
        const a = fromEl.value, b = toEl.value;
        fromEl.value = b; toEl.value = a; convert();
    }

    function validate(){
        const v = valueEl.value.trim();
        const valid = v !== '' && !Number.isNaN(Number(v));
        btnConvert.disabled = !valid;
        return valid;
    }

    async function convert(){
        if(!validate()) { resultEl.textContent = '–'; resultMetaEl.textContent = 'Enter a valid number'; return; }
        const payload = {
            category: currentCategory,
            fromUnit: fromEl.value,
            toUnit: toEl.value,
            value: Number(valueEl.value)
        };

        setLoading(true); setStatus('Contacting server…');

        // Abort stale requests when user types fast
        convert.controller?.abort();
        const controller = new AbortController();
        convert.controller = controller;

        try{
            const res = await fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
                signal: controller.signal
            });
            if(!res.ok){
                const text = await res.text().catch(()=>res.statusText);
                throw new Error(text || `HTTP ${res.status}`);
            }
            const data = await res.json();
            // Expected server response shape:
            // { result: number, unit?: string, precision?: number, meta?: string }
            const unit = data.unit || payload.toUnit.toUpperCase();
            const precision = Number.isFinite(data.precision) ? data.precision : 6;
            const value = Number(data.result);
            const pretty = Number.isFinite(value) ? value.toFixed(Math.min(precision, 12)).replace(/\.0+$/,'').replace(/(\.\d*?)0+$/,'$1') : 'NaN';
            resultEl.textContent = `${pretty} ${unit}`;
            resultMetaEl.textContent = data.meta || `${payload.value} ${payload.fromUnit.toUpperCase()} → ${unit}`;
            setStatus('Done');
            $("#last-updated").textContent = `Updated ${now()}`;
        }catch(err){
            if(err.name === 'AbortError'){ return; }
            resultEl.textContent = 'Error';
            resultMetaEl.innerHTML = `<span class="err">${(err && err.message) || 'Request failed'}</span>`;
            setStatus('Failed');
        }finally{
            setLoading(false);
        }
    }

    function setCategory(cat){
        currentCategory = cat;
        fillUnits(cat);
        // Update chips states
        $$('.chip').forEach(c=>{
            const on = c.dataset.cat === cat;
            c.setAttribute('aria-pressed', on);
            c.setAttribute('aria-selected', on);
        });
        setStatus(`Category: ${cat}`);
        resultEl.textContent = '–'; resultMetaEl.textContent = 'No conversion yet';
    }

    // --- Events -----------------------------------------------------------------
    $$('.chip').forEach(chip=> chip.addEventListener('click', ()=> setCategory(chip.dataset.cat)) );
    $('#swap').addEventListener('click', swapUnits);
    btnConvert.addEventListener('click', convert);
    btnReset.addEventListener('click', ()=>{ valueEl.value=''; validate(); resultEl.textContent='–'; resultMetaEl.textContent='No conversion yet'; valueEl.focus(); });
    valueEl.addEventListener('input', debounce(()=>{ if(validate()) convert(); }, 400));
    fromEl.addEventListener('change', ()=> validate() && convert());
    toEl.addEventListener('change', ()=> validate() && convert());

    // --- Init -------------------------------------------------------------------
    setCategory(currentCategory);
    validate();
</script>
