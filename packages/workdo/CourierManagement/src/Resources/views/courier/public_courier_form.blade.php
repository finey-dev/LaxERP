@extends('courier-management::layouts.master')
@section('page-title')
    {{ __('Create Courier Request') }}
@endsection
@push('css')
    <style>
        .error-msg {
            color: red;
        }
    </style>
@endpush
@php
    $admin_settings = getAdminAllSetting();
    $company_settings = getCompanyAllSetting($workspace->created_by, $workspace->id);
@endphp
@section('content')
    <div class="auth-wrapper create-ticket justify-content-between flex-column auth-v1">
        <div class="bg-auth-side"></div>
        <div class="appointment-bg request-image1 border border-primary rounded-circle p-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="104" height="86" viewBox="0 0 104 86" fill="none">
                <path d="M84.3997 43.4635V17.7325C84.3997 17.0624 83.9883 16.4609 83.3635 16.218L42.8393 0.461157C42.4604 0.313688 42.0402 0.313688 41.6613 0.461157L1.1377 16.218C0.513094 16.4609 0.101562 17.0624 0.101562 17.7325V64.5256C0.101562 65.1957 0.512891 65.7971 1.1377 66.0401L41.6615 81.7969C41.8511 81.8706 42.0507 81.9074 42.2504 81.9074C42.4501 81.9074 42.65 81.8706 42.8393 81.7969L63.7353 73.6721C67.1505 80.7524 74.4014 85.6493 82.7745 85.6493C94.4222 85.6493 103.898 76.1734 103.898 64.5256C103.898 53.4244 95.291 44.2959 84.3997 43.4635ZM42.2504 31.7459L33.3192 28.2732C45.333 23.6026 57.346 18.9313 69.3593 14.2598L78.2907 17.7325L42.2504 31.7459ZM19.6253 22.9485L55.6644 8.93472L64.8755 12.5162C52.8621 17.1877 40.8488 21.8592 28.8354 26.5296L19.6253 22.9485ZM16.7665 25.3239L27.2104 29.3847V37.1719L22.7449 33.3684C22.3204 33.0071 21.7411 32.8884 21.2087 33.0538L16.7668 34.4352L16.7665 25.3239ZM42.2504 3.71908L51.1804 7.1913L15.1415 21.2051L6.21055 17.7325L42.2504 3.71908ZM3.35156 20.1078L13.5165 24.0602V35.9118C13.5165 36.5975 13.8458 37.2493 14.3971 37.655C14.9488 38.0606 15.6685 38.1809 16.3223 37.9771L21.3192 36.4232L26.8942 41.1715C27.2927 41.5111 27.7942 41.6878 28.3004 41.6878C28.6073 41.6878 28.9159 41.6228 29.2039 41.49C29.9672 41.1375 30.4606 40.3665 30.4606 39.5251V30.6486L40.6256 34.601V77.907L3.35156 63.4141V20.1078ZM43.8754 77.9072V34.601L81.1497 20.1078V43.4635C70.2583 44.2959 61.6509 53.4246 61.6509 64.5256C61.6509 66.6525 61.9682 68.7063 62.5554 70.6439L43.8754 77.9072ZM82.7747 82.3993C72.9188 82.3993 64.9009 74.3812 64.9009 64.5256C64.9009 54.6697 72.919 46.6516 82.7747 46.6516C92.6303 46.6516 100.648 54.6699 100.648 64.5256C100.648 74.3812 92.6303 82.3993 82.7747 82.3993ZM95.3038 54.4845C94.4629 53.6437 93.3451 53.1808 92.1562 53.1808C90.9673 53.1808 89.8495 53.6437 89.0084 54.4845L78.7463 64.747L75.9375 61.2124C75.089 60.1452 73.8197 59.533 72.4551 59.533C71.4555 59.533 70.4736 59.876 69.6902 60.4988C68.7607 61.2378 68.1742 62.2946 68.0396 63.4746C67.9047 64.6545 68.2376 65.8164 68.9764 66.7457L74.7492 74.0101C75.5848 75.176 76.9358 75.8703 78.374 75.8703C79.5602 75.8703 80.6762 75.4074 81.5169 74.5668L95.304 60.7795C97.0393 59.044 97.0393 56.22 95.3038 54.4845ZM93.0057 58.4816L79.2185 72.2689C78.9279 72.5596 78.5988 72.6205 78.3737 72.6205C77.9722 72.6205 77.6108 72.4324 77.3819 72.1044C77.3624 72.0767 77.3425 72.0497 77.3214 72.0235L71.5205 64.7238C71.3217 64.4738 71.2321 64.1609 71.2684 63.8437C71.3046 63.5262 71.4624 63.2418 71.7127 63.043C71.9828 62.828 72.2656 62.783 72.4551 62.783C72.8232 62.783 73.165 62.9477 73.3931 63.2345L77.335 68.195C77.623 68.5576 78.0522 68.78 78.5147 68.8064C78.9789 68.8336 79.4288 68.6606 79.7562 68.3331L91.3063 56.7826C91.5332 56.5557 91.8353 56.4308 92.1562 56.4308C92.4771 56.4308 92.7788 56.5557 93.0057 56.7826C93.4743 57.251 93.4743 58.0132 93.0057 58.4816Z" fill="black"/>
            </svg>
        </div>
        <div class="appointment-bg request-image2 border border-primary rounded-circle p-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="98" height="62" viewBox="0 0 98 62" fill="none">
                <path d="M97.6525 30.6587C97.6362 30.5938 97.6037 30.5125 97.5713 30.4475C97.49 30.285 97.3925 30.155 97.2788 30.0249L85.7575 15.27C85.4487 14.8799 84.9775 14.6362 84.4738 14.6362H72.6925L75.6987 3.00119C75.8288 2.51371 75.715 1.9937 75.4062 1.58745C75.1138 1.19746 74.6263 0.96994 74.1225 0.96994H16.3375C15.6063 0.96994 14.9562 1.45752 14.7612 2.18869L11.8051 13.6558H3.66266C2.76526 13.6558 2.03766 14.3834 2.03766 15.2808C2.03766 16.1782 2.76526 16.9058 3.66266 16.9058H10.9602L10.9588 16.9112L9.54495 22.4037L9.54396 22.4077H1.875C0.9776 22.4077 0.25 23.1353 0.25 24.0327C0.25 24.9301 0.9776 25.6577 1.875 25.6577H8.69903L6.94499 32.4624L2.08626 51.2474C1.95623 51.735 2.06999 52.2549 2.37875 52.645C2.6875 53.035 3.15871 53.2787 3.66246 53.2787H12.2956C13.0764 57.6821 16.922 61.0387 21.5456 61.0387C26.1692 61.0387 30.0154 57.6821 30.7963 53.2787H61.4475C61.4574 53.2787 61.4661 53.2741 61.4759 53.2739H67.3187C68.0976 57.6796 71.9442 61.0387 76.5695 61.0387C81.1947 61.0387 85.042 57.6796 85.821 53.2739H90.4379C90.4486 53.2741 90.4593 53.2787 90.47 53.2787C90.5838 53.2787 90.6975 53.2625 90.795 53.2463C90.8925 53.23 90.99 53.1975 91.0713 53.165C91.2175 53.1 91.3475 53.0187 91.4775 52.9212C91.5425 52.8724 91.5913 52.8237 91.64 52.7749C91.77 52.645 91.8675 52.4987 91.9325 52.3363C91.9377 52.3275 91.9383 52.3172 91.9429 52.3085C91.9536 52.2844 91.9606 52.2601 91.9703 52.2354C91.9891 52.1841 92.0101 52.1359 92.03 52.0762C92.0462 52.0438 92.0625 51.995 92.0625 51.9463L97.685 31.6175C97.7337 31.4712 97.75 31.325 97.75 31.1787C97.75 31.065 97.7337 30.9675 97.7175 30.87C97.7012 30.7887 97.685 30.7237 97.6525 30.6587ZM9.80501 34.4125L9.806 34.4087H31.6146C32.512 34.4087 33.2396 33.6819 33.2396 32.7837C33.2396 31.8863 32.512 31.1587 31.6146 31.1587H10.6348L12.0628 25.6577H33.4022C34.2996 25.6577 35.0272 24.9301 35.0272 24.0327C35.0272 23.1353 34.2996 22.4077 33.4022 22.4077H12.9077L12.9087 22.4037L14.3225 16.9112L14.3238 16.9058H27.5052C28.4026 16.9058 29.1302 16.1782 29.1302 15.2808C29.1302 14.3834 28.4026 13.6558 27.5052 13.6558H15.1689L17.605 4.21994H72.0262L69.0037 15.9362L65.1525 30.8537L60.1963 50.0287H30.8022C30.0367 45.6068 26.1825 42.2306 21.5456 42.2306C16.9087 42.2306 13.055 45.6068 12.2897 50.0287H5.75877L9.80501 34.4125ZM21.5456 57.7887C18.1601 57.7887 15.4066 55.039 15.3945 51.6562C15.3945 51.6553 15.395 51.6546 15.395 51.6537V51.6375C15.3949 51.6341 15.3931 51.6314 15.393 51.628C15.3968 48.2383 18.1551 45.4806 21.5456 45.4806C24.9361 45.4806 27.6938 48.2372 27.6988 51.6257C27.6987 51.63 27.6964 51.6333 27.6963 51.6375V51.6537C27.6963 51.6554 27.6973 51.6568 27.6973 51.6585C27.684 55.0402 24.9311 57.7887 21.5456 57.7887ZM76.5695 57.7887C73.1826 57.7887 70.4277 55.0367 70.418 51.652C70.418 51.6509 70.4186 51.65 70.4186 51.6489C70.4186 51.6457 70.4167 51.6429 70.4167 51.6396C70.4167 51.6379 70.4162 51.6363 70.4162 51.6347C70.4162 48.2418 73.1766 45.4806 76.5695 45.4806C79.9631 45.4806 82.7235 48.2418 82.7235 51.6347C82.7235 55.0275 79.9631 57.7887 76.5695 57.7887ZM89.22 50.0239H85.8253C85.058 45.6044 81.2046 42.2306 76.5695 42.2306C71.9343 42.2306 68.0815 45.6044 67.3143 50.0239H63.545L67.9963 32.82H93.98L89.22 50.0239Z" fill="black"/>
            </svg>
        </div>
        <div class="appointment-bg request-image3 border border-primary rounded-circle p-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="65" height="65" viewBox="0 0 65 65" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M21.2035 9.80811C21.2035 9.44373 21.3978 9.10713 21.7133 8.925L35.3903 1.02878C35.7057 0.84665 36.0944 0.84665 36.4099 1.02878L41.2653 3.83206L45.2314 6.12172L50.0869 8.925C50.4023 9.10713 50.5967 9.44373 50.5967 9.80811V19.8348C52.2921 21.2326 53.9898 22.613 55.6192 23.5385C56.2806 23.9141 56.9258 24.2153 57.552 24.3945C57.6807 22.7351 59.0787 21.4171 60.7697 21.4171C62.5446 21.4171 63.997 22.8693 63.997 24.6443V35.2896C63.997 37.0647 62.5447 38.517 60.7697 38.517C59.057 38.517 57.6448 37.1648 57.5476 35.4753C56.6082 35.6684 55.8922 36.0704 55.1475 36.4548C53.9005 37.0984 52.5944 37.7171 50.4986 37.7171H37.9587C36.5798 37.7171 35.4509 36.5886 35.4509 35.2095C35.4509 35.0495 35.4662 34.8929 35.4952 34.741H32.842C31.4629 34.741 30.3344 33.6125 30.3344 32.2334C30.3344 32.0628 30.3516 31.8963 30.3845 31.735C29.5952 31.6117 28.9247 31.1166 28.5594 30.4362L21.7133 26.4837C21.3978 26.3015 21.2035 25.9649 21.2035 25.6007V9.80811ZM38.7164 4.71504L35.9001 3.08915L24.2623 9.80811C24.2623 9.80811 25.4559 10.4972 27.0786 11.434L38.7164 4.71504ZM42.6824 7.0047L40.7556 5.89243L29.1178 12.6114L31.0446 13.7237L42.6824 7.0047ZM47.5379 9.80811L44.7216 8.18209L33.0838 14.9011C34.7065 15.8379 35.9001 16.5271 35.9001 16.5271L47.5379 9.80811ZM48.5575 18.1667V11.5741L36.9197 18.293V23.7737H40.3192C42.2684 23.7737 43.8116 23.7107 44.9116 23.5509C45.2997 23.4944 45.6213 23.4349 45.8694 23.3549C45.8369 23.3205 45.8033 23.2866 45.7696 23.2557C45.4697 22.9802 45.0495 22.6742 44.5089 22.3289C41.876 20.6474 42.4992 17.7222 43.5476 15.9414C43.6848 15.7083 43.909 15.5393 44.1708 15.4717C44.4325 15.4038 44.7105 15.4428 44.9435 15.58C46.1115 16.2677 47.3259 17.1799 48.5575 18.1667ZM57.5424 26.487C56.5977 26.2917 55.6146 25.8811 54.6121 25.3116C52.7722 24.2665 50.8419 22.6805 48.9282 21.1022C47.5492 19.9647 46.1793 18.8315 44.8566 17.9381C44.5441 18.8569 44.5322 19.9243 45.6065 20.6103C46.7341 21.3304 47.432 21.9541 47.765 22.4481C48.2124 23.1114 48.2065 23.6976 47.9761 24.1836C47.7802 24.597 47.3294 25.0198 46.5123 25.2838C45.3861 25.6478 43.2865 25.8129 40.3192 25.8129H35.9001H32.4472C32.1895 25.8129 31.9788 26.0237 31.9788 26.2813C31.9788 26.539 32.1896 26.7498 32.4472 26.7498H42.14C42.7027 26.7498 43.1596 27.2067 43.1596 27.7694C43.1596 28.3321 42.7027 28.789 42.14 28.789H30.7707C30.5131 28.789 30.3023 28.9998 30.3023 29.2574C30.3023 29.3063 30.3098 29.3535 30.3238 29.3978C30.3247 29.4005 30.3256 29.4033 30.3265 29.406C30.3891 29.5913 30.565 29.7258 30.7707 29.7258H42.9263C43.489 29.7258 43.9459 30.1826 43.9459 30.7454C43.9459 31.3081 43.489 31.765 42.9263 31.765H32.842C32.5844 31.765 32.3736 31.9758 32.3736 32.2334C32.3736 32.4911 32.5844 32.7019 32.842 32.7019H44.3853C44.948 32.7019 45.4049 33.1587 45.4049 33.7215C45.4049 34.2842 44.948 34.741 44.3853 34.741H37.9587C37.7011 34.741 37.4902 34.9519 37.4902 35.2095C37.4902 35.4672 37.7011 35.678 37.9587 35.678H50.4986C52.1758 35.678 53.2143 35.1577 54.2121 34.6427C55.2251 34.1199 56.2061 33.6047 57.5424 33.4053V26.487ZM59.5815 24.6443V35.2896C59.5815 35.9431 60.1162 36.4777 60.7673 36.4777H60.7697C61.4232 36.4777 61.9578 35.9431 61.9578 35.2896V24.6443C61.9578 23.9909 61.4232 23.4563 60.7697 23.4563C60.1163 23.4563 59.5815 23.9909 59.5815 24.6443ZM23.2427 11.5741V25.012L28.5581 28.0809C28.8639 27.5099 29.3843 27.0693 30.0096 26.8686C29.9639 26.6801 29.9396 26.4834 29.9396 26.2813C29.9396 24.9021 31.0679 23.7737 32.4472 23.7737H34.8805V18.293L32.0642 16.6671V21.1208C32.0642 21.485 31.87 21.8216 31.5545 22.0039C31.2389 22.186 30.8503 22.186 30.5349 22.0039L26.5688 19.7142C26.2533 19.5321 26.059 19.1955 26.059 18.8313V13.2L23.2427 11.5741ZM30.025 15.4897L28.0982 14.3774V18.2426L30.025 19.355V15.4897ZM59.9533 24.8854C59.9533 24.4344 60.3187 24.069 60.7697 24.069C61.2206 24.069 61.5863 24.4344 61.5863 24.8854C61.5863 25.3364 61.2206 25.702 60.7697 25.702C60.3187 25.702 59.9533 25.3364 59.9533 24.8854ZM14.5944 58.0598L15.2426 59.2053C15.2431 59.2062 15.2436 59.207 15.2441 59.2079C15.874 60.3282 15.4744 61.7576 14.3605 62.3869L11.8554 63.8067C11.8544 63.8072 11.8534 63.8078 11.8524 63.8083C10.7321 64.4382 9.30247 64.0386 8.67311 62.9243L1.30657 49.9078C0.670975 48.7864 1.07053 47.3554 2.18586 46.7254L4.69384 45.3056L4.69473 45.3051C5.80954 44.6755 7.23967 45.0711 7.87451 46.1859L7.87578 46.1882L8.38915 47.0953L14.8341 43.3638C16.8399 42.2037 18.8911 41.4557 21.0942 42.1711C23.1993 42.8537 26.6358 44.3259 30.1431 44.9597C30.1881 44.9679 30.2325 44.979 30.2759 44.993C32.1164 45.589 33.2327 47.4515 32.73 49.3205C32.6809 49.5032 32.6218 49.6778 32.5528 49.8438C32.8284 49.7459 33.0997 49.6199 33.3675 49.4639L42.2998 44.2616C45.5938 42.3427 47.7923 42.5719 49.0353 43.4058C50.1217 44.1347 50.6277 45.3779 50.5952 46.5995C50.5624 47.835 49.9698 49.0132 49.0008 49.6098L32.793 59.5888C31.0559 60.6609 29.1533 60.913 27.1823 60.3872L27.1812 60.3869L15.8319 57.3453L14.5944 58.0598ZM13.4667 60.2076L6.10217 47.1946L6.10115 47.1929C6.01971 47.0523 5.83911 47.0009 5.69764 47.0806L3.19055 48.5001L3.18966 48.5006C3.04896 48.58 3.00052 48.7608 3.08056 48.9022L3.08095 48.9028L10.4477 61.9199L10.4482 61.9207C10.5282 62.0623 10.7102 62.1108 10.8524 62.0312L13.355 60.6128L13.3563 60.612C13.4979 60.5319 13.5465 60.3499 13.4667 60.2076ZM13.5899 56.285L15.178 55.368C15.4122 55.2328 15.6906 55.1962 15.9517 55.2662L27.7082 58.4169C29.1179 58.793 30.4796 58.6203 31.7219 57.8536L31.7229 57.8529L47.9316 47.8735C48.349 47.6163 48.5426 47.0777 48.5567 46.5453C48.5984 44.9773 47.0215 43.8709 43.3263 46.0237L34.3942 51.2257C32.9346 52.0763 31.4048 52.3125 29.7376 52.047C29.7363 52.0469 29.7352 52.0466 29.734 52.0465C29.734 52.0465 28.598 51.8613 28.5843 51.8585C28.5838 51.8584 28.5834 51.8584 28.5829 51.8583C26.5646 51.4439 24.6926 50.8345 22.6155 50.2762C22.072 50.1302 21.7493 49.5704 21.8954 49.0269C22.0414 48.4835 22.6012 48.1608 23.1447 48.3069C25.1663 48.8502 26.9887 49.4461 28.9511 49.8521C29.8704 49.9519 30.5489 49.58 30.7606 48.7915L30.7608 48.791C30.9744 47.997 30.4741 47.2304 29.7048 46.9527C26.1219 46.2933 22.6159 44.8082 20.465 44.1108L20.4645 44.1107C18.8326 43.5807 17.3409 44.2697 15.8551 45.129L9.39347 48.8701L13.5899 56.285ZM5.27693 48.4984C5.84549 48.4138 6.37339 48.8063 6.45801 49.3749C6.5392 49.9435 6.14665 50.4748 5.57809 50.556C5.00953 50.6406 4.48163 50.2446 4.397 49.6761C4.31582 49.1075 4.70837 48.5796 5.27693 48.4984Z" fill="black"/>
            </svg>
        </div>
        <div class="auth-content">
            <nav class="navbar navbar-expand-md bg-primary default ">
                <div class="container-fluid pe-sm-4 pe-3">
                    <a class="navbar-brand" href="#">
                        <img src="{{ check_file(company_setting('logo_light', $workspace->created_by, $workspace->id)) ? get_file(company_setting('logo_light', $workspace->created_by, $workspace->id)) : get_file(!empty(admin_setting('logo_light')) ? admin_setting('logo_light') : 'uploads/logo/logo_light.png') }}{{ '?' . time() }}"
                            class="navbar-brand-img auth-navbar-brand">
                    </a>
                    <button class="navbar-toggler p-1 border-white" type="button" aria-expanded="false">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse">
                        <ul class="navbar-nav align-items-center ms-auto   gap-3 ">
                            <li class="nav-item">
                                <a class="nav-link text-white p-0"
                                    href="{{ route('find.courier', $workspace->slug) }}">{{ __('Track Courier') }}</a>
                            </li>

                        </ul>
                    </div>
                </div>
            </nav>

            <div class="row align-items-center justify-content-center text-start">
                <div class="col-xl-10">
                    {{ Form::open(['route' => ['store.public.courier.request'], 'method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate']) }}
                    <div class="card rounded-4">
                        <div class="ticket-title text-center bg-primary">
                            <h4 class="text-white  mb-0 text-capitalize">{{ __('Public Courier') }}</h4>
                        </div>
                        <input type="text" value="{{ $workspace->id }}" hidden name="workspace_id">
                        <div class="card-body w-100 p-md-4 p-3">
                            <div class="mb-3 border-bottom">
                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label for="sender_name"
                                            class="form-label">{{ __('Sender Name') }}</label><x-required></x-required>
                                        <input type="text" name="sender_name" id="sender_name" class="form-control"
                                            placeholder="Enter Sender Name" value="{{ old('sender_name') }}" required>

                                        <span class="error-msg">
                                            @error('sender_name')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <x-mobile divClass="col-sm-6" type="number" name="sender_mobileno"
                                        class="form-control" label="{{ __('Sender Mobile Number') }}"
                                        placeholder="{{ __('Enter Sender Mobile Number') }}" id="mobileField"
                                        required></x-mobile>

                                    <div class="form-group col-sm-6">
                                                {!! Form::label('Sender Email Address', __('Sender Email Address'), ['class' => 'form-label']) !!}<x-required></x-required>
                                                {!! Form::email('sender_email_address', old('sender_email_address'), [
                                                    'class' => 'form-control',
                                                    'required' => 'required',
                                                    'placeholder' => __('Enter Sender Email Address'),
                                                ]) !!}
                                        <div class=" text-xs text-danger mt-1">
                                            {{ __('Using this email you can track your courier') }}
                                        </div>
                                        <span class="error-msg">
                                            @error('sender_email_address')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                    @if (module_is_active('CustomField') && !$customFields->isEmpty())
                                        <div class="form-group col-md-4">
                                            <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                                                @include('custom-field::formBuilder')
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3 border-bottom">
                                <div class="title">
                                    <h4 class="mb-3">{{ __('Delivery Details') }}</h4>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label for="receiver_name" class="form-label">{{ __('Receiver Name') }}<span
                                                class="text-danger pl-1">*</span></label>
                                        <input type="text" name="receiver_name" id="receiver_name"
                                            class="form-control" placeholder="Enter Receiver Name"
                                            value="{{ old('receiver_name') }}" required>
                                        <span class="error-msg">
                                            @error('receiver_name')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                    <x-mobile divClass="col-sm-6" type="number" name="receiver_mobileno"
                                        class="form-control" label="{{ __('Receiver Mobile Number') }}"
                                        placeholder="{{ __('Enter Receiver Mobile Number') }}" id="mobileField"
                                        required></x-mobile>

                                    <div class="form-group col-sm-6">
                                        <label for="branch_id"
                                            class="form-label">{{ __('Service Type') }}</label><x-required></x-required>

                                        <select class="form-select" name="service_type" required>
                                            <option selected disabled>{{ __('Select Service Type') }}</option>
                                            @foreach ($serviceType as $type)
                                                <option value="{{ $type['id'] }}">{{ $type['service_type'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="error-msg">
                                            @error('service_type')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label for="from_branch"
                                            class="form-label">{{ __('Source Branch') }}</label><x-required></x-required>

                                        <select class="form-select" aria-label="Default select example" required
                                            name="source_branch" id="source_branch">
                                            <option selected disabled>{{ __('Select Source') }}</option>
                                            @foreach ($courierBranch as $branch)
                                                <option value="{{ $branch['id'] }}">
                                                    {{ $branch['branch_name'] . ' , ' . $branch['city'] . ' , ' . $branch['state'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="error-msg">
                                            @error('source_branch')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label for="to_branch"
                                            class="form-label">{{ __('Destination Branch') }}</label><x-required></x-required>

                                        <select class="form-select" aria-label="Default select example" required
                                            name="destination_branch" id="destination_branch">
                                            <option selected disabled>Select Destination</option>
                                        </select>
                                        <span class="error-msg">
                                            @error('destination_branch')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="receiver_address"
                                            class="form-label">{{ __('Receiver Address') }}</label><x-required></x-required>
                                        <textarea name="receiver_address" id="receiver_address" class="form-control" rows="3"
                                            placeholder="Enter Receiver Address" required>{{ old('receiver_address') }}</textarea>
                                        <span class="error-msg">
                                            @error('receiver_address')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="title">
                                    <h4 class="mb-3">{{ __('Package Information') }}</h4>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label for="package_title"
                                            class="form-label">{{ __('Package Title') }}</label><x-required></x-required>
                                        <input type="text" name="package_title" id="package_title"
                                            class="form-control" placeholder="Enter Package Title"
                                            value="{{ old('package_title') }}" required>
                                        <span class="error-msg">
                                            @error('package_title')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label for="package_category"
                                            class="form-label">{{ __('Select Package Category') }}</label><x-required></x-required>
                                        <select class="form-select" aria-label="Default select example"
                                            name="package_category" required>
                                            <option selected disabled>{{ __('Select Package Category') }}</option>
                                            @foreach ($packageCategory as $category)
                                                <option value="{{ $category['id'] }}">
                                                    {{ $category['category'] }}
                                                </option>
                                            @endforeach
                                            <span class="error-msg">
                                                @error('package_category')
                                                    {{ $message }}
                                                @enderror
                                            </span>
                                        </select>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label for="weight"
                                            class="form-label">{{ __('Weight') }}</label><x-required></x-required>
                                        <input type="text" name="weight" id="weight" class="form-control"
                                            placeholder="Enter Package Weight" value="{{ old('weight') }}" required>
                                        <span class="error-msg">
                                            @error('weight')
                                                {{ $message }}
                                            @enderror
                                        </span>

                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label for="height"
                                            class="form-label">{{ __('Height') }}</label><x-required></x-required>
                                        <input type="text" name="height" id="height" class="form-control"
                                            placeholder="Enter Package Height" value="{{ old('height') }}" required>
                                        <span class="error-msg">
                                            @error('height')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label for="width"
                                            class="form-label">{{ __('Width') }}</label><x-required></x-required>
                                        <input type="text" name="width" id="width" class="form-control"
                                            placeholder="Enter Package Width" value="{{ old('width') }}" required>
                                        <span class="error-msg">
                                            @error('width')
                                                {{ $message }}
                                            @enderror
                                        </span>

                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label for="price" class="form-label">{{ __('Price') }}<span
                                                class="text-danger pl-1">*</span></label>
                                        <input type="text" name="price" id="price" class="form-control"
                                            placeholder="Enter Price" value="{{ old('price') }}" required>
                                        <span class="error-msg">
                                            @error('price')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="delivery_date"
                                                class="form-label">{{ __('Expected Delivery Date') }}</label><x-required></x-required>
                                            <input type="date" name="delivery_date" id="delivery_date"
                                                class="form-control" autocomplete="off"
                                                placeholder="Select Expected Delivery Date"
                                                value="{{ old('delivery_date') }}" required>
                                            <span class="error-msg">
                                                @error('delivery_date')
                                                    {{ $message }}
                                                @enderror
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-12 form-group">
                                        <label for="package_description"
                                            class="form-label">{{ __('Package Description') }}</label><x-required></x-required>
                                        <textarea name="package_description" id="package_description" class="form-control" rows="3"
                                            placeholder="Enter Package Description" required>{{ old('package_description') }}</textarea>
                                        <span class="error-msg">
                                            @error('package_description')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" id="submit"
                                            class="btn  btn-primary">{{ __('Create Courier') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <div class="auth-footer create-ticket-footer text-center w-100 bg-primary mt-4">
            <div class="container-fluid">
                <p class="p-2 mb-0 text-white">
                    {{ !empty(company_setting('footer_text', $workspace->created_by, $workspace->id)) ? company_setting('footer_text', $workspace->created_by, $workspace->id) : admin_setting('footer_text') }}
                </p>
            </div>
        </div>
    </div>
@endsection

{{-- Today Date script --}}
@push('script')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script type="text/javascript">
        $('form').on('submit', function() {
            // Get the selected date from the datepicker
            var selectedDate = $('#datepicker').datepicker('getDate');

            // Format the date as YYYY-MM-DD
            var formattedDate = $.datepicker.formatDate('yy-mm-dd', selectedDate);

            // Set the formatted date as the value of the date input field
            $('#datepicker').val(formattedDate);
        });
    </script>


    {{-- ajax for get the destination branch --}}
    <script>
        $(document).ready(function() {
            $('#source_branch').change(function() {
                var branchId = $('#source_branch').val();
                console.log(branchId);
                $.ajax({
                    url: "{{ route('courier.get.branch.publicrequest', ['workspaceId' => $workspace->id]) }}",
                    type: 'POST',
                    data: {
                        branchId: branchId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        var branch = response;
                        $('#destination_branch').empty();
                        $('#destination_branch').html(
                            '<option selected disabled>Select Destination</option>');
                        for (var i = 0; i < branch.length; i++) {
                            var optionText = branch[i].branch_name + ', ' + branch[i].city +
                                ', ' + branch[i].state;
                            $('#destination_branch').append('<option value="' + branch[i].id +
                                '">' + optionText + '</option>');
                        }

                    },
                });
            });
        });

        var today = new Date().toISOString().split('T')[0];
        document.getElementById('delivery_date').min = today;

        $(document).ready(function() {
            let wasSmallScreen = $(window).width() <= 767;
            $('.navbar-toggler').click(function() {
                if ($(window).width() <= 767) {
                    $('.navbar-collapse').toggleClass('show');
                    $('body').toggleClass('no_scroll');
                }
            });

            $(window).resize(function() {
                const isSmallScreen = $(window).width() <= 767;

                if (wasSmallScreen && !isSmallScreen) {
                    $('.navbar-collapse').removeClass('show');
                    $('body').removeClass('no_scroll');
                    location.reload();
                }
                wasSmallScreen = isSmallScreen;
            });
        });
    </script>
    {{-- end --}}
@endpush
