<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <title>Worksheet</title>

    <style>

        @page {
            margin: 0;
            padding: 0;
        }

        body{
            font-family: DejaVu Sans;
            margin:0;
            padding:0;
            color:#222;
            background:#dfdfdf;
            padding-top: 20px;
        }

        .header{
            background: #9ECB3C;
            color:#000000;
            padding:40px;
            position:relative;
            margin-top: 0;
        }

        .header h1{
            margin:0;
            font-weight: 700;
            line-height: 100%;
            letter-spacing: 0%;
            vertical-align: middle;
            text-transform:uppercase;
        }

        .header p{
            margin-top:10px;
            font-size:18px;
        }

        .content-wrapper{
            background:#f7f7f7;
        }

        .notebook{
            background:#dfdfdf;
            padding: 20px 40px;
            position:relative;
        }

        // .top-area{
        //     margin-bottom:30px;
        // }

        .top-area h2{
            font-family: Roboto;
            font-weight: 600;
            font-size: 20px;
            line-height: 154%;
            letter-spacing: 0%;
            color: #000000;
            margin: 0;
            margin-bottom: 12px;
        }

        .top-area p{
            font-weight: 400;
            font-size: 16px;
            line-height: 176%;
            letter-spacing: 0%;
            vertical-align: middle;
            color: #676767;
            margin: 0;
            text-align: left;
        }

        .section{
            
            border-bottom:1px solid #ddd;
        }

        .section h3{
            margin-bottom:10px;
            color:#111;
            font-size:22px;
        }

        .question{
            margin-top:15px;
            /* line-height:1.8; */
        }

        .answer-title{
            font-weight: 600;
            font-size: 20px;
            line-height: 154%;
            letter-spacing: 0%;
            color: #000000;
            margin: 0;
            margin-bottom: 12px;
        }

        .answer-box{
            height: auto;
            border: 1px dashed #757575;
            border-radius: 5px;
            padding: 12px;
            font-family: Roboto;
            font-weight: 400;
            font-size: 16px;
            line-height: 176%;
            letter-spacing: 0%;
            vertical-align: middle;
            color: #5A5A5A;
            outline: none;
            resize: none;
            background: transparent;
        }

        .business-title{
            background:#9ECB3C;
            color:#000000 !important;
            padding:12px 20px;
            margin-top:30px;
            /* margin-bottom:25px; */
            font-size:24px;
            border-radius:8px;
        }

        .page-break{
            page-break-after: always;
        }
    </style>
</head>

<body>

    <div class="header">

        <h1>
            DOMEWORK BUSINESS PLAN
            WORKSHEET
        </h1>

        <p>
            {{ $session_info->session_name }}
        </p>

    </div>


    <div class="content-wrapper">

        <div class="notebook">

            <div class="top-area">

                <h2>
                    Hi, {{ auth()->user()->name }}
                </h2>

                <p>
                    {{ now()->format('jS F, Y') }}
                </p>

            </div>


            {{-- DOMEWORKS --}}
            @foreach($assigned_domework as $item)

                <div class="section">

                    <h3 class="business-title">
                        {{ $item->domework->title }}
                    </h3>

                    <p>
                        {{ $item->domework->description }}
                    </p>

                    <div class="question">

                        <strong>Question</strong>

                        <p>
                            {{ $item->domework->question }}
                        </p>

                    </div>


                    <div class="answer-title">
                        Enter your response here
                    </div>

                    <div class="answer-box">

                        {!! nl2br(e($item->domework_answer)) !!}

                    </div>

                </div>

            @endforeach


            {{-- BUSINESS PLANS --}}
            @foreach($assigned_businessplan as $item)

                <div class="business-title">

                    {{ $item->businessPlan->title }}

                </div>

                <div class="section">

                    <p>
                        {{ $item->businessPlan->description }}
                    </p>

                    <div class="answer-title">
                        {{ $session_info->session_name }} | Notes
                    </div>

                    <div class="answer-box">

                        {!! nl2br(e($item->businessplan_answer)) !!}

                    </div>

                </div>

            @endforeach

        </div>

    </div>  

</body>
</html>