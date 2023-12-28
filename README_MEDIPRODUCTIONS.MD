## ################################
## #####  REQUIREMENTS  ###########
## ################################

PHP         -> 8.0.30
Mysql       -> 8.2.0
Node js     -> 18.18.2
Composer    -> 2


## ##########################
## #####  LAUCNH  ###########
## ##########################

1/
symfony server:start -d

2/
stripe listen --events checkout.session.completed,checkout.session.async_payment_failed,checkout.session.async_payment_succeeded --forward-to https://localhost/payment/notify/unsafe/stripe_checkout_session_with_sca