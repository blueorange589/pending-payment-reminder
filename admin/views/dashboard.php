<div id="prfwapp" class="fin-container">
	<div class="fin-head">
		<div class="fin-head-left">
			<span><?php esc_html_e( 'Pending Payments', 'prfw' ); ?></span>
		</div>
		<div class="fin-head-right">
			<div class="fin-timeframe">
				<button @click="exportCSV" id="export" class="fin-button flr"><?php _e( 'Export', 'prfw' ); ?></button>
			</div>
		</div>
	</div>

	<div class="fin-content">

		<div class="orders-container">
			<div class="orders-content">
				<table class="fin-table fin-table-thin fin-table-export" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th><?php _e( 'ID', 'prfw' ); ?></th>
							<th><?php _e('Date created', 'prfw'); ?></th>
							<th><?php _e('Status', 'prfw'); ?></th>
							<th><?php _e('Payment Method', 'prfw'); ?></th>
							<th><?php _e('Customer', 'prfw'); ?></th>
							<th><?php _e('Country', 'prfw'); ?></th>
							<th class="tar"><?php _e('Tax', 'prfw'); ?> ({{currencySymbol}})</th>
							<th class="tar"><?php _e('Shipping', 'prfw'); ?> ({{currencySymbol}})</th>
							<th class="tar"><?php _e('Shipping Tax', 'prfw'); ?> ({{currencySymbol}})</th>
							<th class="tar"><?php _e('Subtotal', 'prfw'); ?> ({{currencySymbol}})</th>
							<th class="tar"><?php _e('Total', 'prfw'); ?> ({{currencySymbol}})</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(order, index) in orders">
							<td><a :href="order.url" target="_blank">#{{order.id}}</a></td>
							<td>{{order.date}}</td>
							<td>{{printStatus(order.status)}}</td>
							<td>{{order.pm}}</td>
							<td>{{order.cus}}</td>
							<td>{{order.geo}}</td>
							<td class="tar">{{floatFix(order.tax)}}</td>
							<td class="tar">{{floatFix(order.shipamount)}}</td>
							<td class="tar">{{floatFix(order.shiptax)}}</td>
							<td class="tar">{{floatFix(order.st)}}</td>
							<td class="tar">{{floatFix(order.total)}}</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<th class="tal b"><?php _e('Totals', 'prfw'); ?></th>
							<th colspan="5">
							<th>{{floatFix(totals.tax)}}</th>
							<th>{{floatFix(totals.shipamount)}}</th>
							<th>{{floatFix(totals.shiptax)}}</th>
							<th>{{floatFix(totals.st)}}</th>
							<th class="b">{{floatFix(totals.total)}}</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		
	</div>
</div>
