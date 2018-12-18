<?php
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2017112800217369",

		//商户私钥，您的原始格式RSA私钥
		'merchant_private_key' => "MIIEowIBAAKCAQEA0AzrdKAy1ry0U6zkZ1kY3igJ5oLm0q2pUoBVDxwFHIL60hwhRF8NvoiUjt3nYHOgDVUCxKDPlRDIFQIYFLFxs0Kacxq4DuYECR3INYbEJlGtklyuvf9XCZJ4KkJrdSEviSi49ubVKTnUzIN9xuVJP4zRQMFypeqFvNCqB5b3KPpEgC1c2Vtka0R9vj9egcKgjB/z76wMyLnP0AhjALiSNBzvlr/xIbHh9RM78iLb+fAmyGPDsKXcyHPeJEMG4FZEUQlEAWfFjthff1dhMvGYRADp+z4KJLA+zMnCp1KIMTbu92aTJHc/5axX21iskULLGK4BQAPcWawnB/5WM5dxSwIDAQABAoIBAGj/jDkgvGeZdtyf7emmkqFTzaNTPxDmagTkFYl5l67K/7DYV4W8/x/AqFDt9t67m/P+ilYX3ouZm5DWrLaM0xgoLfCXdHq4ZSMAr9ErwYShRhsOwXXMDu4ayKBzQu4rIGV1ctvQpZWXxSit1+VwRCZZeCXZO3Y2xOYE3TrVWsmHk6wcjvkOe8e55DZifIAlkTiTpt/M2xcsmn5FZ7KpozqP4CBQRFU7crElKBq1FVUSpZ4EAiSst2KKfuBZ5VQw7NRW+xEbv2kBZPN23g2aOXT7EY5e04fXm1FNL6SKafttr9BiTN3RKg+YbHt+9bnC2O9KBbZi4x56qMWGlNDGGkkCgYEA8TxEz6Xvdsr0gwYq+Sk+xD/o1i5rfkRngABNhyLH/vfnLdnUMoz7A9PsHrLY3oA0qfmOO/NH9J7FHoSuGDiz2h5BE+BcGRaB1+SQYwpKJu/3SwDSRpTES9LWZK+hBFR1o0YLStIDvxFtMfBfs1a5UesriaEdXIH0nfO5swT7sP8CgYEA3MizmAwtJIovtECJtjNS7O4JQNorVaKwF4zrTbyxpGSoiRJz0O212rYqBcXc2SqBjGQiMYy42+aT7xoDvYdJfXAbe4oAUKz+oIVM3gjDYDas/hF5tFMhekWi1TwvIYjnEpokuqn5haMx07ZMbKJNc75FWjKm7U5xOTdxkL4fs7UCgYAjtk5hhykqJuWHuBQIisiV+PgZLQoQyUOhOWpAxS9iWYTJzuOREDng2TCtYsCkP8qMFzutOFjAp/h6ih3TzCEj+zKbJK29sQ1LzrRpVQ6hMQkk+1wlwsREjVPb1/w4TeCUaTwmuWyAjq3hpLomLIKG1A+/8jAMj9iMwZYOIIC6NQKBgQCCc6gCB/fIZknnhFaaLFssGT2obwA52iWX1LDXvs1rp/oY+6ko4ruX3hiYTp++RSpEjInToyU68VOpbEKXgs8+abYjWZf0EzNkHz8a1iyytbujZyn1DUVznJ+oxKXId76otCFNlc8UUifHwygG7CM9Z0hNEi6lerrVptfYQS40HQKBgFkgcMHeZx2JRUz7L4XwQH3APTCSOplds0BOoNp0DhHabEdSo44BUDN9fyg77HjxhFN1IN6hgTk+P3dsWm6yIE93msL0CuqOgUIxYJjj94sj/4fO5PLBtydXPpEQa6Jk/pxHwrtxRcnbEOebC0/gUuzUc7ezSIFXAlpnxcFTQ/cS",

//		//异步通知地址
//		'notify_url' => "http://".$_SERVER['HTTP_HOST']."/purchase/".config('wx_config.call_back_url').'/weixin.order',
//
//		//同步跳转
//		'return_url' =>"http://".$_SERVER['HTTP_HOST'].url('Payment/payComplete'),

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA0AzrdKAy1ry0U6zkZ1kY3igJ5oLm0q2pUoBVDxwFHIL60hwhRF8NvoiUjt3nYHOgDVUCxKDPlRDIFQIYFLFxs0Kacxq4DuYECR3INYbEJlGtklyuvf9XCZJ4KkJrdSEviSi49ubVKTnUzIN9xuVJP4zRQMFypeqFvNCqB5b3KPpEgC1c2Vtka0R9vj9egcKgjB/z76wMyLnP0AhjALiSNBzvlr/xIbHh9RM78iLb+fAmyGPDsKXcyHPeJEMG4FZEUQlEAWfFjthff1dhMvGYRADp+z4KJLA+zMnCp1KIMTbu92aTJHc/5axX21iskULLGK4BQAPcWawnB/5WM5dxSwIDAQAB",
		
	
);