AimeosCheckoutStandard.setupAddressForms = function() {

	$(".checkout-standard-address .item-address").has(".header input:not(:checked)").find(".form-list").hide();

	/* Address form slide up/down when selected */
	$(".checkout-standard-address-billing,.checkout-standard-address-delivery").on("click", ".header input",
		function(ev) {
			$(".form-list", ev.delegateTarget).slideUp(400);
			$(".item-address", ev.delegateTarget).has(this).find(".form-list").slideDown(400);
		});
};


AimeosCheckoutStandard.setupSalutationCompany = function() {}
