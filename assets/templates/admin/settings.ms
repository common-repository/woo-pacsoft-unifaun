<div class="wrap">
	<h1>{{ title }}
    	    <a href="https://wetail.se/support/" class="page-title-action" target="_blank">Support</a>
    	    <a href="https://docs.wetail.io/woocommerce-pacsoft-unifaun-integration/" class="page-title-action" target="_blank">FAQ</a>
    	    {{# buy }}
                <a href="https://wetail.se/service/intergrationer/woocommerce-unifaun/" class="button-primary page-title-action" target="_blank">Order License</a>
            {{/ buy }}
     </h1>

	<!--<div class="welcome-panel">
		<a class="welcome-panel-close" href="#">Dismiss</a>
		<h2>Welcome to Unifaun/Pacsoft for WooCommerce!</h2>
		<p class="about-description">Some dummy text here.</p>

	</div>-->

	{{# hasTabs }}
	<h2 class="nav-tab-wrapper">
		{{# tabs }}
		<a class="nav-tab nav-tab-{{ tab.name }} {{# tab.selected }}nav-tab-active{{/ tab.selected }} {{ tab.class }}" href="options-general.php?page=woocommerce-pacsoft&tab={{ tab.name }}">{{ tab.title }}</a>
		{{/ tabs }}
	</h2>
	{{/ hasTabs }}

	<form method="post" action="options.php">
		{{{ hidden }}}
		{{# sections }}
			{{# section.title }}
				<h2 class="title">{{ . }}</h2>
			{{/ section.title }}

			{{# section.description }}
				<p>{{{ . }}}</p>
			{{/ section.description }}

			<table class="form-table">
				<tbody>
					{{# section.fields }}
					<tr>
						<th scope="row">{{ field.title }}</th>
						<td>
						    {{# field.info }}
						        <span>{{{ field.value }}}</span>
                                {{# field.tooltip }}{{#formatTooltip}}{{ . }}{{/formatTooltip}}{{/ field.tooltip }}
                                {{# field.html_tooltip }}{{#formatHtmlTooltip}}{{{ . }}}{{/formatHtmlTooltip}}{{/ field.html_tooltip }}
                            {{/ field.info }}
							{{# field.text }}
							    <input type="text" name="{{ field.name }}" value="{{ field.value }}" class="{{ field.class }}" autocomplete="off" placeholder="{{ field.placeholder }}">
							    {{# field.tooltip }}{{#formatTooltip}}{{ . }}{{/formatTooltip}}{{/ field.tooltip }}
                                {{# field.html_tooltip }}{{#formatHtmlTooltip}}{{{ . }}}{{/formatHtmlTooltip}}{{/ field.html_tooltip }}
							{{/ field.text }}

							{{# field.password }}
							    <input type="password" name="{{ name }}" value="{{ selected }}" class="{{ class }}" autocomplete="off" placeholder="{{ placeholder }}">
							    {{# field.tooltip }}{{#formatTooltip}}{{ . }}{{/formatTooltip}}{{/ field.tooltip }}
                                {{# field.html_tooltip }}{{#formatHtmlTooltip}}{{{ . }}}{{/formatHtmlTooltip}}{{/ field.html_tooltip }}
							{{/ field.password }}

							{{# field.dropdown }}
								<select name="{{ field.name }}" class="{{ field.class }}">
									{{# field.options }}
										<option value="{{ option.value }}" {{# option.selected }}selected="selected"{{/ option.selected }}>{{ option.label }}</option>
									{{/ field.options }}
								</select>
								{{# field.tooltip }}{{#formatTooltip}}{{ . }}{{/formatTooltip}}{{/ field.tooltip }}
                                {{# field.html_tooltip }}{{#formatHtmlTooltip}}{{{ . }}}{{/formatHtmlTooltip}}{{/ field.html_tooltip }}
							{{/ field.dropdown }}

							{{# field.radio }}
								{{# field.options }}
									<p>
										<label>
											<input type="radio" name="{{ field.name }}" value="{{ value }}" {{# selected }}checked="checked"{{/ selected }} class="{{ class }}"> {{{ label }}}
										</label>
									</p>
								{{/ field.options }}
								{{# field.tooltip }}{{#formatTooltip}}{{ . }}{{/formatTooltip}}{{/ field.tooltip }}
                                {{# field.html_tooltip }}{{#formatHtmlTooltip}}{{{ . }}}{{/formatHtmlTooltip}}{{/ field.html_tooltip }}
							{{/ field.radio }}

							{{# field.checkbox }}
								<p>
									<input type="hidden" name="{{ field.name }}" value="0">
									<label>
										<input type="checkbox" name="{{ field.name }}" value="1" {{# field.checked }}checked="checked"{{/ field.checked }} class="{{ class }}"> {{{ field.label }}}
									</label>
								</p>
								{{# field.tooltip }}{{#formatTooltip}}{{ . }}{{/formatTooltip}}{{/ field.tooltip }}
                                {{# field.html_tooltip }}{{#formatHtmlTooltip}}{{{ . }}}{{/formatHtmlTooltip}}{{/ field.html_tooltip }}
							{{/ field.checkbox }}

							{{# field.checkboxes }}
								<fieldset>
									<legend class="screen-reader-text">
										<span>{{ title }}</span>
									</legend>

									{{# field.options }}
										<label>
											<input type="hidden" name="{{ option.name }}" value="0">
											<input type="checkbox" name="{{ option.name }}" value="1" {{# option.checked }}checked="checked"{{/ option.checked }} class="{{ option.class }}"> {{{ option.label }}}
										</label><br>
										{{# option.description }}
										<span class="description">{{{ . }}}</span><br>
										{{/ option.description }}
									{{/ field.options }}
								</fieldset>
							{{/ field.checkboxes }}

							{{# field.table }}
							    {{# field.tooltip }}{{#formatTooltip}}{{ . }}{{/formatTooltip}}{{/ field.tooltip }}
                                {{# field.html_tooltip }}{{#formatHtmlTooltip}}{{{ . }}}{{/formatHtmlTooltip}}{{/ field.html_tooltip }}
								<table class="wp-list-table widefat fixed striped posts {{ class }}">
									<thead>
										<tr>
											{{# table.columns }}
											<th class="column-{{ column.name }}" style="text-align: center;">{{{ column.title }}}</th>
											{{/ table.columns }}
										</tr>
									</thead>
									<tbody {{# table.id }}id="{{ . }}"{{/ table.id }}>
										{{# table.rows }}
											{{> admin/settings/table-row }}
										{{/ table.rows }}
									</tbody>
								</table>

								{{# table.addRowButton }}
								<p><a href="#" class="button {{ table.addRowButtonClass }}">Add row</a></p>
								{{/ table.addRowButton }}
							{{/ field.table }}

							{{# field.html }}
								{{{ . }}}
							{{/ field.html }}

							{{# field.button }}
							<a href="#" class="button button-primary button-hero pacsoft-admin-action" {{# field.data }}data-{{ key }}="{{ value }}" {{/ field.data }}style="text-align: center; width: 240px">{{ field.button.text }}</a> <span class="spinner pacsoft-spinner hero"></span>
							{{/ field.button }}

							{{# field.after }}
							{{{ . }}}
							{{/ field.after }}

							{{# field.description }}
								<p class="description">{{{ . }}}</p>
							{{/ field.description }}
						</td>
					</tr>
					{{/ section.fields }}
				</tbody>
			</table>
		{{/ sections }}

		{{# saveButton }}
		<p class="submit">
			<button class="button-primary">{{# i18n }}Save changes{{/ i18n }}</button>
		</p>
		{{/ saveButton }}
	</form>
</div>
