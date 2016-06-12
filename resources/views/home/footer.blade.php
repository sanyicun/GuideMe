<footer class="aui-nav" id="aui-footer">
        <ul class="aui-bar-tab">
            <li class="active-info" id="helper_list" tapmode onclick="switchTab(0)">
                <span class="aui-iconfont aui-icon-crownfill"></span>
                <p>附近</p>
            </li>
            <li id="ask_list" tapmode onclick="switchTab(1)">
                <span class="aui-iconfont aui-icon-commandfill"></span>
                <p>问答</p>
            </li>
            <li id="my" tapmode onclick="switchTab(2)">
                <span class="aui-iconfont aui-icon-peoplefill"></span>
                <p>我的</p>
            </li>
        </ul>
<script type="text/javascript">
    function switchTab(index){
        window.location = "./tab=" + index;
    }
</script>
 </footer>