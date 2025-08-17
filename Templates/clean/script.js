document.addEventListener('DOMContentLoaded', () => {

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
        for (const [key, category] of Object.entries(WP_Unit_Converter.categories)) {
            const button = document.createElement('button');
            button.className = 'category-tab';
            button.dataset.category = key;
            button.innerHTML = `<img src="${category.icon}" /> ${category.name}`;
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
        const units = WP_Unit_Converter.categories[currentCategory].units;
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

            const response = await fetch(WP_Unit_Converter.backendUrl, {
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
