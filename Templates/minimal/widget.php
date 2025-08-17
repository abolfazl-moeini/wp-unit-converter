<div class="wpuc-widget">
    <form class="convert-form">

        <label><?php _e( 'Category:', 'unit-converter' ) ?></label>
        <select class="category" name="category">
            <option value="">Select</option>
            <?php foreach ( \WPUnitConverterPlugin::instance()->unit_categories() as $key => $category ): ?>
                <option value="<?php echo esc_attr( $key ) ?>"><?php echo esc_html( $category['name'] ) ?></option>
            <?php endforeach; ?>
        </select>

        <label><?php _e( 'From:', 'unit-converter' ) ?></label>
        <select class="fromUnit" name="from"></select>

        <label><?php _e( 'To:', 'unit-converter' ) ?></label>
        <select class="toUnit" name="to"></select>

        <label><?php _e( 'Value', 'unit-converter' ) ?></label>
        <input type="number" class="value" name="value" placeholder="<?php _e( 'Enter Value', 'unit-converter' ) ?>">

        <button class="convert" type="submit"><?php _e( 'Convert', 'unit-converter' ) ?></button>
    </form>

    <div class="result"></div>

</div>
