<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/web3/1.8.1/web3.min.js" integrity="sha512-vtUOC0YIaNm/UutU7yfnwqnF9LOYnXtpHe2kwi1nvJNloeGQuncNBiEKP/3Ww3D62USAhbXGsnYpAYoiDsa+wA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <title>Document</title>
</head>

<style>
    .overlay {
        display: none;
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        z-index: 999;
        background: rgba(255, 255, 255, 0.8) url("/loader.gif") center no-repeat;
    }

    body {
        text-align: center;
    }

    /* Turn off scrollbar when body element has the loading class */
    body.loading {
        overflow: hidden;
    }

    /* Make spinner image visible when body element has the loading class */
    body.loading .overlay {
        display: block;
    }
</style>

<body>
    <div class="container">
        <div class="row mt-5 bg-light">
            <div class="col-md-6 offset-3 mt-5">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Enter BNB Amount</label>
                        <input type="number" name="price_in_busd" placeholder="Enter BNB Amount" id="price_in_busd" class="form-control">
                    </div>
                    <!-- <div class="mb-3">
                        <label class="form-label">Total Token</label>
                        <input type="number" class="form-control" name="token" id="token">
                    </div> -->
                    <div class="mb-3 col-md-4 offset-4">
                        <button type="button" name="send_bnb" id="send_bnb" class="form-control btn btn-primary">Send Bnb</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="overlay"></div>
</body>

<script>
    $(document).ready(function() {

        $("#send_bnb").one('click', function(event) {

            event.preventDefault();

            web = new Web3(window.ethereum);

            if (typeof(window.ethereum) == 'undefined') {
                swal("Oops!", "Metamask is not installed !", "error");
            } else {

                const bnb = $("#price_in_busd").val();
                const weiBnb = (bnb * 1e18);
                const value = weiBnb.toString();

                web.eth.requestAccounts().then(account => {

                    const address = account[0];
                    web.eth.getBalance(address).then(balance => {

                        if (balance >= value && value != 0) {

                            web.eth.getChainId().then(chainid => {

                                if (chainid == 97 || chainid == '0x61') {

                                    const transactionParameters = {

                                        nonce: '0x00',
                                        from: address,
                                        to: '0xD79acCf08c4bc29fd36150c8144d5452207778D8',
                                        value: web.utils.toHex(value),
                                        chainId: '97',
                                        gasPrice: 0,
                                    };

                                    $("#send_bnb").slideUp();

                                    const txHash = ethereum.request({

                                        method: 'eth_sendTransaction',
                                        params: [transactionParameters],

                                    }).then(txHash => {

                                        if (txHash) {

                                            var stop = 0;
                                            $("body").addClass("loading");
                                            var timerForConfirmation = setInterval(() => {

                                                try {

                                                    web.eth.getTransactionReceipt(txHash).then(response => {

                                                        if (response.status == true && stop == 0) {

                                                            stop = 1;
                                                            clearInterval(timerForConfirmation);
                                                            ResetFunctionality();
                                                            swal("Great!", "Transaction is Successfull !", "success").then(res => { location.reload(); });

                                                        } else if (stop == 0 && response.status === false) {

                                                            stop = 1;
                                                            clearInterval(timerForConfirmation);
                                                            ResetFunctionality();
                                                            swal("Oops!", "Transaction Failed !", "error").then(res => { location.reload(); });
                                                        }
                                                    });
                                                } catch (err) {

                                                    console.log(err)
                                                }
                                            }, 3000);
                                        }
                                    }).catch(err => {

                                        ResetFunctionality();
                                        swal("Oops!", "Transaction is Declined !", "error").then(res => { location.reload(); });
                                    });
                                } else {

                                    swal("Oops!", "Change Your ChaiID To Smart Chain Test Network !", "error");
                                }

                            })
                        } else {

                            swal("Oops!", "You Have Not Sufficient Balance", "error").then(res => {
                                location.reload();
                            });
                        }
                    })
                }).catch(err => {

                    swal("Oops!", "Your Account Address Not Find !", "error");
                });

            }
        });

        function ResetFunctionality() {

            $("body").removeClass("loading");
            $("#price_in_busd").val('');
            $("#send_bnb").slideDown();
            
        }

        ethereum.on('chainChanged', (chainId) => {

            if (chainId == '0x61' || chainId == '97') {

            } else {

                swal("Oops!", "Don't Change Your ChainId", "error");
            }
        });

        ethereum.on('accountsChanged', (accounts) => {

            swal("Oops!", "Don't Change Your Account", "error");
        });

    });
</script>

</html>