<html>

<head>
    <style>
        @media screen and (max-width: 80px) {
            .news {
                position: relative;

                margin-left: auto;
                margin-right: auto;
                margin-top: 400px;
            }

            .text1 {
                box-shadow: none !important;
                position: relative;
                margin-left: auto;
                margin-right: auto;
            }
        }

        .color {
            /* background: #347fd0; */
            background-color: var(--cart);
        }

        .news {
            box-shadow: inset 0 -15px 30px rgba(10, 4, 60, 0.4), 0 5px 10px rgba(10, 20, 100, 0.5);
            width: 100%;
            height: 40px;
            margin-top: 0px;
            overflow: hidden;

            border-radius: 4px;
            padding: 1px;
            position: relative;
            z-index: 2;
        }

        .news span {
            float: left;
            color: #fff;
            padding: 9px;
            /* box-shadow: inset 0 -15px 30px rgba(0, 0, 0, 0.4); */
            font: 16px 'Raleway', Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            cursor: pointer;
        }

        /* .text1 {
            box-shadow: none !important;
            width: 90%
        } */

        .scrolling-text {
            animation: scroll-left 30s linear infinite;
            width: 90%
        }

        @keyframes scroll-left {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }
    </style>
</head>

<body>

    <div class="news color">
        <?php if ($announcement) { ?>
            <span class="scrolling-text">
                <?php echo $announcement['description']; ?>
            </span>
        <?php } else { ?>
            <span class="scrolling-text">
                Nu sunt anunturi disponibile.
            </span>
        <?php } ?>
    </div>



</body>

</html>