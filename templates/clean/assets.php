<style>
    :root {
        --primary-color: #007bff;
        --background-color: #f4f7fc;
        --card-background: #ffffff;
        --text-color: #333;
        --border-color: #dde4ee;
        --input-background: #f8f9fa;
        --shadow-color: rgba(0, 0, 0, 0.08);
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: var(--background-color);
        color: var(--text-color);
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
    }

    .converter-wrapper {
        width: 100%;
        max-width: 600px;
        background: var(--card-background);
        border-radius: 16px;
        box-shadow: 0 8px 24px var(--shadow-color);
        padding: 2rem;
        box-sizing: border-box;
        margin: 1rem;
    }

    h1 {
        text-align: center;
        margin-bottom: 2rem;
        font-weight: 600;
        color: var(--text-color);
    }

    .category-tabs {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 2rem;
    }

    .category-tab {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        border: 1px solid var(--border-color);
        border-radius: 20px;
        background: transparent;
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--text-color);
        transition: all 0.2s ease-in-out;
    }

    .category-tab:hover {
        background: var(--input-background);
        border-color: #c9d3e4;
    }

    .category-tab.active {
        background-color: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    .category-tab svg {
        width: 20px;
        height: 20px;
    }

    .converter-body {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 1rem;
    }

    .converter-side {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    label {
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .unit-select,
    .value-input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 1rem;
        background: var(--input-background);
        box-sizing: border-box;
    }

    .unit-select {
        margin-bottom: 0.5rem;
    }

    .value-input:focus,
    .unit-select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15);
    }

    .value-input[readonly] {
        background-color: #e9ecef;
        cursor: not-allowed;
    }

    .swap-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 44px; /* Align with input height */
        padding-bottom: 0.5rem; /* Align baseline */
        color: var(--primary-color);
        cursor: default;
    }

    .error-message {
        color: #d93025;
        text-align: center;
        margin-top: 1rem;
        height: 20px;
        font-size: 0.9rem;
    }

    /* Responsive Design */
    @media (max-width: 600px) {
        .converter-body {
            flex-direction: column;
            align-items: stretch;
        }

        .swap-icon {
            transform: rotate(90deg);
            margin: 0.5rem 0;
            padding: 0;
        }

        h1 {
            font-size: 1.5rem;
        }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', () => {

        // --- CONFIGURATION ---
        const CONFIG = {
            // The backend endpoint that will handle the conversion logic.
            backendUrl: 'http://localhost:3000/api/convert', // <-- IMPORTANT: Change this to your actual backend URL

            // Defines the available unit categories and their respective units.
            categories: {
                length: {
                    name: 'Length',
                    icon: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>`,
                    units: {
                        'Meters': 'm',
                        'Centimeters': 'cm',
                        'Inches': 'in',
                        'Yards': 'yd',
                        'Kilometers': 'km',
                        'Miles': 'mi'
                    }
                },
                speed: {
                    name: 'Speed',
                    icon: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L6 22 12 18 18 22z"/></svg>`,
                    units: {
                        'Meters/sec': 'm/s',
                        'Kilometers/hr': 'km/h',
                        'Miles/hr': 'mph',
                        'Knots': 'knots'
                    }
                },
                temperature: {
                    name: 'Temperature',
                    icon: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"/></svg>`,
                    units: {
                        'Celsius': 'C',
                        'Fahrenheit': 'F',
                        'Kelvin': 'K'
                    }
                },
                volume: {
                    name: 'Volume',
                    icon: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>`,
                    units: {
                        'Liters': 'l',
                        'Milliliters': 'ml',
                        'Gallons (US)': 'gal',
                        'Cubic Meters': 'm3'
                    }
                },
                weight: {
                    name: 'Weight',
                    icon: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22a4 4 0 0 0 4-4H8a4 4 0 0 0 4 4z"/><path d="M12 2a4 4 0 0 0-4 4h8a4 4 0 0 0-4-4z"/><path d="M12 6v12"/></svg>`,
                    units: {
                        'Kilograms': 'kg',
                        'Grams': 'g',
                        'Pounds': 'lb',
                        'Ounces': 'oz'
                    }
                }
            }
        };

        // --- STATE ---
        let currentCategory = 'length';

        // --- DOM ELEMENTS ---
        const categoryTabsContainer = document.getElementById('categoryTabs');
        const fromUnitSelect = document.getElementById('fromUnit');
        const toUnitSelect = document.getElementById('toUnit');
        const fromValueInput = document.getElementById('fromValue');
        const toValueInput = document.getElementById('toValue');
        const errorMessageDiv = document.getElementById('errorMessage');

        // --- FUNCTIONS ---

        /**
         * Populates the category tabs based on the CONFIG object.
         */
        function createCategoryTabs() {
            categoryTabsContainer.innerHTML = '';
            for (const [key, category] of Object.entries(CONFIG.categories)) {
                const button = document.createElement('button');
                button.className = 'category-tab';
                button.dataset.category = key;
                button.innerHTML = `${category.icon} ${category.name}`;
                if (key === currentCategory) {
                    button.classList.add('active');
                }
                button.addEventListener('click', () => {
                    currentCategory = key;
                    updateActiveCategoryTab();
                    populateUnitSelectors();
                    clearValues();
                });
                categoryTabsContainer.appendChild(button);
            }
        }

        /**
         * Updates the visual state of the active category tab.
         */
        function updateActiveCategoryTab() {
            document.querySelectorAll('.category-tab').forEach(tab => {
                tab.classList.toggle('active', tab.dataset.category === currentCategory);
            });
        }

        /**
         * Populates the 'From' and 'To' unit dropdowns for the current category.
         */
        function populateUnitSelectors() {
            const units = CONFIG.categories[currentCategory].units;
            fromUnitSelect.innerHTML = '';
            toUnitSelect.innerHTML = '';

            for (const [name, value] of Object.entries(units)) {
                const fromOption = new Option(name, value);
                const toOption = new Option(name, value);
                fromUnitSelect.add(fromOption);
                toUnitSelect.add(toOption);
            }

            // Set default selections (e.g., first and second unit)
            fromUnitSelect.selectedIndex = 0;
            toUnitSelect.selectedIndex = 1;
        }

        /**
         * Clears input and result fields.
         */
        function clearValues() {
            fromValueInput.value = '';
            toValueInput.value = 'Result';
            errorMessageDiv.textContent = '';
        }

        /**
         * Sends the conversion request to the backend.
         * This function is debounced to avoid sending requests on every keystroke.
         */
        async function handleConversion() {
            const value = parseFloat(fromValueInput.value);
            const fromUnit = fromUnitSelect.value;
            const toUnit = toUnitSelect.value;

            errorMessageDiv.textContent = '';

            if (isNaN(value)) {
                toValueInput.value = 'Result';
                return;
            }

            if (fromUnit === toUnit) {
                toValueInput.value = value;
                return;
            }

            try {
                // Show loading state if desired
                toValueInput.value = 'Converting...';

                const response = await fetch(CONFIG.backendUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        value: value,
                        fromUnit: fromUnit,
                        toUnit: toUnit,
                        category: currentCategory
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || `HTTP error! Status: ${response.status}`);
                }

                const data = await response.json();
                toValueInput.value = data.result;

            } catch (error) {
                console.error('Conversion Error:', error);
                errorMessageDiv.textContent = `Error: ${error.message}`;
                toValueInput.value = 'Error';
            }
        }

        /**
         * Debounce function to limit the rate at which a function gets called.
         */
        function debounce(func, delay) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), delay);
            };
        }

        // --- EVENT LISTENERS ---
        const debouncedConversion = debounce(handleConversion, 300);

        fromValueInput.addEventListener('input', debouncedConversion);
        fromUnitSelect.addEventListener('change', handleConversion);
        toUnitSelect.addEventListener('change', handleConversion);

        // --- INITIALIZATION ---
        createCategoryTabs();
        populateUnitSelectors();
    });
</script>
