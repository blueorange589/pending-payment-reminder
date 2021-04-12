var app = new Vue({
	el: '#prfwapp',
	data: {
		orders: [],
		totals: [],
		filters: {datestart:'', dateend:'', totalthan: 'greater', total:0, datetype:'date_created', gateway:'', status: 'all'},
		currencySymbol: '',
	},
	mounted: function() {
		this.currencySymbol = prfw.symbol;
		this.siteurl = prfw.siteurl;
		this.getOrders();
		jQuery('.datepicker').datepicker({dateFormat : 'yy-mm-dd'});
	},
	methods: {
		getOrders: function() {
			var self = this;
			prfw.xhr({handler:'orders', process:'getOrders', filters: JSON.stringify(this.filters)}, function (data) { 
				self.filters = data.payload.filters;
				self.orders = data.payload.orders;
				self.totals = data.payload.totals;
			});
		},
		filterOrders: function() {
			if(!prfw.validateForm('form-filter')) { return false; }
			this.filters.datestart = jQuery('#datestart').val();
			this.filters.dateend = jQuery('#dateend').val();
			this.getOrders();
		},
		printStatus: function(str) {
			var string = str.replace('-', ' ');
			return string.charAt(0).toUpperCase() + string.slice(1);
		},
		exportCSV: function() {
			jQuery(".fin-table").tableToCSV();
		},
		floatFix: function(val) {
			if(!val) { return ''; }
			return parseFloat(val).toFixed(2);
		}
	},
	created() {
		this.$root.$refs.app = this;
	}
});