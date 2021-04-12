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
							<th><?php _e('Total', 'prfw'); ?> ({{currencySymbol}})</th>
							<th><?php _e('Status', 'prfw'); ?></th>
							<th><?php _e('Payment Method', 'prfw'); ?></th>
							<th><?php _e('Customer', 'prfw'); ?></th>
							<th><?php _e('Email', 'prfw'); ?></th>
							<th><?php _e('Country', 'prfw'); ?></th>
							<th><?php _e('Last Reminder', 'prfw'); ?></th>
							<th class="tar"><?php _e('Actions', 'prfw'); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(order, index) in orders">
							<td><a :href="order.url" target="_blank">#{{order.id}}</a></td>
							<td>{{order.date}}</td>
							<td>{{floatFix(order.total)}}</td>
							<td>{{printStatus(order.status)}}</td>
							<td>{{order.pm}}</td>
							<td>{{order.cus}}</td>
							<td>{{order.email}}</td>
							<td>{{order.geo}}</td>
							<td>{{formatDate(order.lastReminder, 'dayhour')}}</td>
							<td class="tar">
								<a @click="sendReminder(order.id)"><img src="<?php echo PRFW_BASE_URL; ?>admin/assets/img/paper-plane.svg" class="icon-xs" title="Send reminder email"></a>
								<a @click="cancelTheOrder(order.id)"><img src="<?php echo PRFW_BASE_URL; ?>admin/assets/img/cross.svg" class="icon-xs" title="Cancel the order"></a>
							</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<th class="tal b"><?php _e('Totals', 'prfw'); ?></th>
							<th colspan="5">
							<th class="b">{{getTotal()}}</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>

	</div>
</div>
