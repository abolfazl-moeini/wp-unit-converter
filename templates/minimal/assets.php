<style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f4f4f4; }
    .container { max-width: 400px; margin: auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); display: grid; gap: 15px; }
    label { font-weight: bold; }
    select, input, button { padding: 10px; border: 1px solid #ddd; border-radius: 4px; width: 100%; box-sizing: border-box; }
    button { background: #007bff; color: white; cursor: pointer; }
    button:hover { background: #0056b3; }
    #result { font-size: 18px; text-align: center; }
</style>

<script>
    const units = {
        length: ['m', 'cm', 'in', 'yd', 'ft', 'mi', 'km'],
        speed: ['m/s', 'km/h', 'mph', 'kn'],
        temperature: ['C', 'F', 'K'],
        volume: ['L', 'mL', 'm³', 'gal', 'pt'],
        weight: ['kg', 'g', 'lb', 'oz', 't']
    };

    const categorySelect = document.getElementById('category');
    const fromUnitSelect = document.getElementById('fromUnit');
    const toUnitSelect = document.getElementById('toUnit');
    const valueInput = document.getElementById('value');
    const convertBtn = document.getElementById('convert');
    const resultDiv = document.getElementById('result');

    categorySelect.addEventListener('change', updateUnits);

    function updateUnits() {
        const category = categorySelect.value;
        fromUnitSelect.innerHTML = '';
        toUnitSelect.innerHTML = '';
        if (category && units[category]) {
            units[category].forEach(unit => {
                const option = document.createElement('option');
                option.value = unit;
                option.textContent = unit;
                fromUnitSelect.appendChild(option.cloneNode(true));
                toUnitSelect.appendChild(option);
            });
        }
    }

    convertBtn.addEventListener('click', async () => {
        const category = categorySelect.value;
        const fromUnit = fromUnitSelect.value;
        const toUnit = toUnitSelect.value;
        const value = valueInput.value;

        if (!category || !fromUnit || !toUnit || !value) {
            resultDiv.textContent = 'Fill all fields.';
            return;
        }

        try {
            const response = await fetch(`/convert?category=${category}&from=${fromUnit}&to=${toUnit}&value=${value}`);
            const data = await response.json();
            resultDiv.textContent = `${value} ${fromUnit} = ${data.result} ${toUnit}`;
        } catch (error) {
            resultDiv.textContent = 'Error converting.';
        }
    });
</script>
