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
                        <label class="form-label">Enter USDT Amount</label>
                        <input type="number" name="price_in_busd" placeholder="Enter USDT Amount" id="price_in_busd" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total Token</label>
                        <input type="number" class="form-control" name="total_token" id="total_token">
                    </div>
                    <div class="mb-3 col-md-4 offset-4">
                        <button type="button" name="send_token" id="send_token" class="form-control btn btn-primary">Send Token</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="overlay"></div>
</body>

<script>
    $(document).ready(function() {

        $("#send_token").one('click', function(event) {

            event.preventDefault();

            web = new Web3(window.ethereum);

            if (typeof(window.ethereum) == 'undefined') {
                swal("Oops!", "Metamask is not installed !", "error");
            } else {

                const usdt = $("#price_in_busd").val();
                const weiusdt = (usdt * 1e18);
                console.log(weiusdt)
                const value = weiusdt.toString();

                web.eth.requestAccounts().then(async account => {

                    const address = account[0];
                    await getUserBalance(address).then(async res => {

                        const UserBalance = res;
                        console.log(UserBalance)

                        if (usdt > 0) {

                            var contractAbi = '';
                            var contractAddressToken = '0x337610d27c682E347C9cD60BD4b3b107C9d34dDd';
                            var tokenvalue = '';
                            tokenvalue = tokenvaluecopy = usdt / 100;
                            tokenvalue = tokenvalue * 1e18;
                            tokenvalue = tokenvalue.toLocaleString('fullwide', {
                                useGrouping: false
                            });

                            $("#total_token").val(tokenvaluecopy);

                            web.eth.getChainId().then(async chainid => {

                                if (chainid == 97 || chainid == '0x61') {

                                    $("#send_bnb").slideUp();

                                    await fetchContractAbi(address).then(res => {

                                        contractAbi = res
                                    }).catch(err => {

                                        console.log(err)
                                    });

                                    const myContract = new web.eth.Contract(

                                        JSON.parse(contractAbi),
                                        contractAddressToken
                                    );

                                    if (Number(UserBalance) >= Number(tokenvalue)) {

                                        const tx = myContract.methods.transfer('0x2d97b0F799B68af52879d173F0916AFa6D7b7b11',
                                            tokenvalue);
                                        const transactionParameters = {

                                            nonce: '0x00',
                                            from: address,
                                            to: contractAddressToken,
                                            chainId: '97',
                                            gasPrice: 0,
                                            gas: 0,
                                            data: tx.encodeABI()
                                        };

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
                                                                swal("Great!", "Transaction is Successfull !", "success").then(res => {
                                                                    location.reload();
                                                                });

                                                            } else if (stop == 0 && response.status === false) {

                                                                stop = 1;
                                                                clearInterval(timerForConfirmation);
                                                                ResetFunctionality();
                                                                swal("Oops!", "Transaction Failed !", "error").then(res => {
                                                                    location.reload();
                                                                });
                                                            }
                                                        });
                                                    } catch (err) {

                                                        console.log(err)
                                                    }
                                                }, 3000);
                                            }
                                        }).catch(err => {

                                            ResetFunctionality();
                                            swal("Oops!", "Transaction is Declined !", "error").then(res => {
                                                location.reload();
                                            });
                                        });
                                    } else {

                                        swal("Oops!", "You Have Not Sufficient Token Balance", "error");
                                    }

                                } else {

                                    swal("Oops!", "Change Your ChaiID To Smart Chain Test Network !", "error");
                                }

                            })
                        } else {

                            swal("Oops!", "USDT Amount Can't Be Zero", "error");
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

        function getUserBalance(address) {

            return new Promise((res, rej) => {
                $.ajax({
                    type: 'get',
                    url: window.location.origin + '/fetchUserBalance',
                    data: {
                        userAccountAddress: address
                    },
                    datatype: 'JSON',
                    success: function(data) {
                        if (data) {
                            if (data) {
                                data = JSON.parse(data);
                                if (data[0]) {
                                    return res(data[1]);
                                } else {
                                    return rej(data[1]);
                                }
                            }
                        }
                    }
                });
            });

        }

        function fetchContractAbi(address) {

            return new Promise((res, rej) => {
                $.ajax({
                    type: 'get',
                    url: window.location.origin + '/fetchContractAbi',
                    data: {
                        currency: 0
                    },
                    datatype: 'JSON',
                    success: function(data) {
                        if (data) {
                            if (data) {
                                data = JSON.parse(data);
                                if (data[0]) {
                                    return res(data[1]);
                                } else {
                                    return rej(data[1]);
                                }
                            }
                        }
                    }

                });
            });

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