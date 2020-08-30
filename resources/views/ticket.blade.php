<!DOCTYPE html>
<html style="height: 100%">
    <body style="margin: 0;font-size: 8px;">
    	<div style="padding: 60px 8px 40px 8px;width: 200px;">
    		<hr>
        	<p style='text-align: center;font-size: 14px;margin: 0'>收费明细</p>
            <div style="margin-left: 8px">
                收费ID: {{$orderId}}<br/>
 	       		姓名：{{$studentName}}<br/>
        		年级：{{$grade}}<br/>
        		应该收费：¥{{$totalAmount}}元<br/>
                实际收费：¥{{number_format($realAmount)}}元<br/>
                收费明细：<br/>
                <div style="padding-left: 8px">
                    <table style="text-align: center;">
                        <th>月份</th>
                        <th style="min-width: 50px;">类型</th>
                        <th>金额(元)</th>
                        @foreach($feeDetail as $detail)
                        <tr>
                            <td>{{$detail['month']}}</td>
                            <td style="min-width: 50px;">{{$classList[$detail['type']]->value}}</td>
                            <td>¥{{$detail['amount']}}/</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
                备注: <div style="padding-left: 8px;">{{$remark ?: '暂无备注信息'}}</div>
                操作人：{{$createUser}}<br/>
        	</div>
        	<div style="border-top: #5b5b5b 1px dashed; overflow: hidden; height: 1px; margin-top: 8px;"></div>
        	<p>本票据仅为收费证明。</p>
            <p>打印时间: {{$now}}</p>
            <p>北京书香源兴隆分校 欢迎您～</p>
        	<hr>
    	</div>
    </body>
</html>
