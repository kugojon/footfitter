var vZeroPayPalButton=Class.create();vZeroPayPalButton.prototype={initialize:function(t,e,n,i,o,r,a){this.clientToken=t||!1,this.clientTokenUrl=a,this.storeFrontName=e,this.singleUse=n,this.locale=i,this.amount=0,this.currency=!1,this.client=!1,this.onlyVaultOnVault=r||!1},getClientToken:function(t){return this.clientToken!==!1?t(this.clientToken):window.braintreeClientToken?t(window.braintreeClientToken):void new Ajax.Request(this.clientTokenUrl,{method:"get",onSuccess:function(e){if(e&&(e.responseJSON||e.responseText)){var n=this._parseTransportAsJson(e);if(1==n.success&&"string"==typeof n.client_token)return this.clientToken=n.client_token,window.braintreeClientToken=n.client_token,t(this.clientToken);console.error("We were unable to retrieve a client token from the server to initialize the Braintree flow."),n.error&&console.error(n.error)}}.bind(this),onFailure:function(){console.error("We were unable to retrieve a client token from the server to initialize the Braintree flow.")}.bind(this)})},getClient:function(t){this.client!==!1?"function"==typeof t&&t(this.client):this.getClientToken(function(e){braintree.client.create({authorization:e},function(e,n){return e?void console.log(e):(this.client=n,void t(this.client))}.bind(this))})},setPricing:function(t,e){this.amount=parseFloat(t),this.currency=e},rebuildButton:function(){return!1},addPayPalButton:function(t,e,n,i){var o;if(e=e||$("braintree-paypal-button").innerHTML,n=n||"#paypal-container",i=i||!1,o="string"==typeof n?$$(n).first():n,!o)return console.warn("Unable to locate container "+n+" for PayPal button."),!1;if(i?o.insert(e):o.update(e),!o.select(">button").length)return console.warn("Unable to find valid <button /> element within container."),!1;var r=o.select(">button").first();r.addClassName("braintree-paypal-loading"),r.setAttribute("disabled","disabled"),this.attachPayPalButtonEvent(r,t)},attachPayPalButtonEvent:function(t,e){this.getClient(function(n){braintree.paypal.create({client:n},function(n,i){return n?(console.error("Error creating PayPal:",n),e.onReady=!1,e.paypalErr=n):e.paypalErr=null,"function"==typeof e.onReady&&e.onReady(i),this._attachPayPalButtonEvent(t,i,e)}.bind(this))}.bind(this))},_attachPayPalButtonEvent:function(t,e,n){(t&&e||null!==n.paypalErr)&&(Array.isArray(t)||(t=[t]),t.each(function(t){t.removeClassName("braintree-paypal-loading"),t.removeAttribute("disabled"),Event.stopObserving(t,"click"),Event.observe(t,"click",function(t){return Event.stop(t),null!==n.paypalErr?void alert(Translator.translate("Paypal is not available ("+n.paypalErr.message+"). Please try an alternative payment method.")):"function"!=typeof n.validate?this._tokenizePayPal(e,n):n.validate()?this._tokenizePayPal(e,n):void 0}.bind(this))}.bind(this)))},_tokenizePayPal:function(t,e){var n=this._buildOptions();"object"==typeof e.tokenizeRequest&&(n=Object.extend(n,e.tokenizeRequest)),t.tokenize(n,function(t,n){return t?void("CUSTOMER"!==t.type&&console.error("Error tokenizing:",t)):void("function"==typeof e.onSuccess&&e.onSuccess(n))}.bind(this))},_buildOptions:function(){var t={displayName:this.storeFrontName,amount:this.amount,currency:this.currency,useraction:"commit",flow:this._getFlow()};return this.locale&&(t.locale=this.locale),t},_getFlow:function(){var t;return t=this.singleUse===!0?"checkout":"vault",null!==$("gene_braintree_paypal_store_in_vault")&&this.onlyVaultOnVault&&"vault"==t&&!$("gene_braintree_paypal_store_in_vault").checked&&(t="checkout"),t},_parseTransportAsJson:function(transport){return transport.responseJSON&&"object"==typeof transport.responseJSON?transport.responseJSON:transport.responseText?"object"==typeof JSON&&"function"==typeof JSON.parse?JSON.parse(transport.responseText):eval("("+transport.responseText+")"):{}}};