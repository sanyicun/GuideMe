<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0"/>
    <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
    <title>职涯帮</title>
    <link rel="stylesheet" type="text/css" href="{{asset('/aui/css/aui.css')}}" />
    <style>
        .aui-bar {
            background: #3bafda;
            color: #ffffff
        }
        .aui-nav {
            background-color: none;
        }
        .aui-nav .aui-bar-tab {
            background-color: #fff;
            border-top:1px solid #eee;
        }
        .aui-nav .aui-bar-tab .aui-iconfont{
            font-size: 26px !important;
        }
        .aui-nav .aui-bar-tab .aui-iconfont, .aui-nav .aui-bar-tab p {
            color: #a2a2a2;
        }
    </style>
</head>
<body>
    <header class="aui-bar aui-bar-nav" id="aui-header">
        <span class="aui-iconfont aui-icon-left aui-pull-left" tapmode onclick="closeWin()"></span>
        <div class="aui-title" id="title">帮主</div>
        <a class="aui-pull-right" tapmode onclick="askAdd()">
            提问<span class="aui-iconfont aui-icon-right"></span>
        </a>
    </header>
    <footer class="aui-nav" id="aui-footer">
        <ul class="aui-bar-tab">
            <li class="active-info" id="helper_list" tapmode onclick="randomSwitchBtn(0,'helper_list')">
                <span class="aui-iconfont aui-icon-crownfill"></span>
                <p>帮主</p>
            </li>
            <li id="ask_list" tapmode onclick="randomSwitchBtn(1,'ask_list')">
                <span class="aui-iconfont aui-icon-commandfill"></span>
                <p>问答</p>
            </li>
            <li id="my" tapmode onclick="randomSwitchBtn(2,'my')">
                <span class="aui-iconfont aui-icon-peoplefill"></span>
                <p>我的</p>
            </li>
        </ul>
    </footer>
</body>
<script type="text/javascript" src="{{asset('/aui/script/api.js')}}"></script>
<script type="text/javascript">
    function askAdd(){
        api.openWin({
            name:'ask_add_win',
            url:'ask_add_win.html',
            delay:300
        })
    }
    function closeWin(){
        api.closeWin({});
    }
    apiready = function(){
        api.parseTapmode();
        var header = $api.byId('aui-header');
        $api.fixStatusBar(header);
        openGroup();
    };
    function openGroup(){
        var header = $api.byId('aui-header');
        var headerPos = $api.offset(header);
        var body_h = $api.offset($api.dom('body')).h;
        var footer_h = $api.offset($api.byId('aui-footer')).h;
        api.openFrameGroup({
            name: 'indexGroup',
            scrollEnabled: true,
            rect:{x:0, y:headerPos.h, w:'auto', h:body_h - headerPos.h - footer_h},
            index: 0,
            frames: [{
                name:'helper_list',
                url:'helper_list.html',
                vScrollBarEnabled:false,
                hScrollBarEnabled:false,
                bounces:false
            },{
                name:'ask_list',
                url:'ask_list.html',
                vScrollBarEnabled:false,
                hScrollBarEnabled:false,
                bounces:false
            },{
                name:'my',
                url:'my.html',
                vScrollBarEnabled:false,
                hScrollBarEnabled:false,
                bounces:false
            }]
        }, function (ret, err) {
            $api.removeCls($api.dom('#aui-footer li.active-info'),'active-info');
            $api.addCls($api.byId(''+ret.name+''),'active-info');
        });
    }

    // 随意切换按钮
    function randomSwitchBtn(index,name) {
        $api.removeCls($api.dom('#aui-footer li.active-info'),'active-info');
        $api.addCls($api.byId(''+name+''),'active-info');
        api.setFrameGroupIndex({
            name: 'indexGroup',
            index: index
        });
    }
</script>
</html>