 <!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0"/>
    <title>GuideMe</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('/aui/css/aui.css')}}" />

<style>
.aui-iconfont {
    font-size: 22px !important;
}
/*.aui-bar {
    background: url('./image/yuanxiao.png') no-repeat #e74c3c;
    background-size: 100% auto;
    background-position: 0 bottom;
}*/
</style>

<script type="text/javascript" src="{{asset('/aui/script/api.js')}}" />
<script type="text/javascript" src="{{asset('/aui/script/aui-alert.js')}}" />
<script type="text/javascript">
    apiready = function(){
        alert('hello');
        api.parseTapmode();
        var header = $api.byId('aui-header');
        $api.fixStatusBar(header);
        var headerPos = $api.offset(header);
        var body_h = $api.offset($api.dom('body')).h;
        api.openWin({
            name: 'main',
            url: "{{asset('/aui/html/tpl/jobteacher/index.html')}}",
            bounces: false,
            
        });
        var ajpush = api.require('ajpush');

        ajpush.init(function(ret) {
            if (ret && ret.status){
            }
        });
        ajpush.setListener(
            function(ret) {
                var id = ret.id;
                var title = ret.title;
                var content = ret.content;
                var extra = ret.extra;
                api.notification({
                    notify: {
                        content: content
                    }
                }, function( ret, err ){
                });
            }
        );
    }
    function about() {
        var html = '<p>当前版本 <span class="aui-text-info">V1.1.8</span></p><p>AUI是专门为APICloud用户打造的一款前端UI框架</p>';
        $aui.alert({
            title:'AUI',
            content:html,
            buttons:['确定'],
            radius:6,
            titleColor:'#ff3300',
            contColor:'#333',
            btnColor:''
        },function(ret){
            //处理回调函数
            if(ret){
            }
        })
    }
</script>

</head>
<body>
    <div class="aui-bar aui-bar-nav aui-bar-warning" id="aui-header">FollowMe</div>
</body>


</html>