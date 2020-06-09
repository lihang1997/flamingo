<template>
    <div class="content">
        <div class='header'>

        </div>
        <div class='main'>
            <div class='sidebar'>

            </div>
            <div class='body'>

            </div>
        </div>
    </div>
</template>

<script>

</script>

<style lang='scss' scope>
.content {
    height: calc(100vh);
    background-color: black;
    display: flex;
    flex-direction: column;
    .header{
        width: 100%;
        height: 100px;
        background-color: #e77c8e;
    }
    .main{
        width: 100%;
        flex: 1;
        background-color: #2bae85;
        .sidebar {
            width: 280px;
            background: green;
            height: 100%;
            float: left;
        }
        .body{
            margin-left: 280px;
            background: yellow;
            height: 100%;
        }
    }
}
</style>
