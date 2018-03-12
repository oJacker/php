#RESTful是什么

	本质：一种软件架构风格；核心：面向资源

	解决的问题： 减低开发的负责性，提高系统的可伸缩性

	设计概念和准则
	网络上所有的事物都可以被抽象为资源
	每个资源都有唯一的资源标识，对资源的操作不会改变这些标识
	所有的操作都是无状态的


## Restful中HTTP协议介绍

1.	HTTP是一个属于应用层的协议，特点是简捷、快捷
	schema://host[:port]/path[?query-string[#anchor]]	

	port: 服务端口，默认80
	path: 访问资源的路径
	query-string: 发送给http服务器的数据
	anchor:锚

2. HTTP协议-请求

	组成格式：请求行，消息报头，请求正文

	请求行
	格式如下：Method Request-URI HTTP-Version CRLF

	举例：
	GET / HTTP/1.1CRLF


	请求

	GET： 请求后去REQUEST-URL所标识的资源
	POST：在Request-URI所标识的资源后附加新的数据
	HEAD:请假获取由Request-URL所标识的资源的响应消息报头

	PUT：请求服务存储一个资源，并用Request-URI作为其标识
	DELETE：请求服务器删除Request-URL所标识的资源
	OPTIONS:请求查询服务器的性能，或者查询与资源相关的选项和需求

	响应 组成格式 状态行、消息报头、响应正文

	状态行
	HTTP-Version  Status-Code Reason-Phrase CRLF
	HTTP/1.1 200 ok

	常用状态码：

	
	200 Ok //客户端请求成功
	400 Bad Request //客户端请求有语法错误，不能被服务器所理解
	401 Unauthorized //服务器收到请求，但是拒绝提供服务

	404 Not Found //请求资源不存在
	500 Internal Server Error // 服务器发生不可预期的错误 
	503 Server Unavailable //服务器不能处理客户端的请求


## RESTful 架构与其架构的区别
1. SOAP WebService

	WebService 是一种跨编程语言和夸操作系统平台的远程调试技术


	WebService 通过HTTP协议发送请求和接收结果时采用XML格式封装，并增加了一些特定的HTTP消息投，这个鞋特定的HTTP消息头和XML内容格式就是SOAP协议

2.效率和易用性
	SOAP由于各种需求不断扩充其本身协议的内容，导致在SOAP处理解方面的性能有所下降，同时在易用性方面以及学习成本上也有所增加。

	RESTful 由于其面向资源接口设计以及操作抽象简单化了开发者的不良设计，同时也最大限度的利用了HTTP最初的应用协议设计理念

3.安全性

	RESTful对于资源型服务接口来说很合适，同时特别适合对于效率要求很高，但是对于安全要求不要的场景。

	SOAP的成熟性可言给需要提供多开发语言的，对于安全性要求较高的接口设计带来便利，所以我觉得纯粹说什么设计模式将会占据主导地位没什么意义，关键还是要看应用场景。


## 如何设计RESTfulAPI
1.资源路径（URI）
	在RESTful架构中，每个网址代表一种资源，所有网址中不能有动词，只能是名词，一般来说API中的名称应该使用复数
	https://api.example.com/v1/zoos
2.HTTP动词

3.过滤信息

4.状态码
	服务器向用户返回的状态码和提示信息，使用标准HTTP状态码
	200 OK 服务器成功返回用户请求的数据，该操作是幂等的
	201 CREATED 新建或修改数据成功
	204 NO CONTENT删除数据成功

	400 BAD REQUEST 用户发出的请求有错误，改操作是幂等的；
	401 Unauthorized 表示用户没有认证，无法进行当前操作。
	403 Forbidden 表示用户访问时被禁止的
	422 Unprocesable Entity 当创建一个对象时，发生的一个验证错误
	500 INTERNAL SERVER ERROR 服务器发生错误，用户将无法判断发出的请求是否成功

5.错误处理
	如果状态码是4xx或5xx，就应该向用户返回错误信息，一般来说，返回的信息中将error作为键名，错误信息作为键值即可
	{
		"error" :"参数错误"
	}

6.返回结果

	针对不同操作，服务器向用户返回的结果应该符合以下规范：
	GET / collections: 返回资源对象的列表(数组)
	GET / collections/identity:返回单个资源对象
	POST /collections: 返回新生成的资源对象
	PUT /collections/identity:返回完整的资源对象 
	PATCH / collections/identity:返回被修改的属性
	DELETE /collections/identity: 返回一个空文档



## DHC CLient安装


