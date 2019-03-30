## 一、背景
笔者不时遇到个别同学求助安装靶场系统和工具的安装，本着方便他人的同时也方便自己，便思考是否可以简化靶场系统和工具安装方式，发现容器非常适合。

便抽了一些时间将一些常用的靶场系统进行了封装，并提供一个可视化操作界面进行管理，容器+可视化管理也就是这个`autowebsafe`项目了。

`autowebsafe`项目主要意义是降低新手入门的门槛,降低靶场系统和工具安装的复杂度。

实现原理主要是通过docker+frp+php脚本实现，包含了 permeate渗透系统、XSS Platform、DVWA等项目。

容器地址如下：
```
https://cloud.docker.com/u/daxia/repository/docker/daxia/websafe
```

下面主要介绍使用方法和注意事项

## 二、注意事项
因为这个项目是用到了Docker容器和PHP脚本以及frp穿透工具，因此在使用此项目前必须安装好以下环境:
1. Docker
2. Nginx 
3. PHP7
4. FRP



## 三、安装步骤
安装步骤大致是首先下载源代码到本地，然后配置一个虚拟主机，再安装docker，再去下载frp，详细步骤如下。

### 3.1 下载代码

笔者已经将代码放到GitHub中，下载代码命令如下，注意需要安装好git

```
git clone https://github.com/78778443/autowebsafe.git
```

## 3.2 配置虚拟主机

下载代码完成之后，需要增加nginx配置，参考配置如下:
```
    server {
        listen       80;
        server_name  autowebsafe.localhost;
        root  /Users/song/mycode/safe/autowebsafe/app/;

        location / {
            index index.html index.htm index.php;
        }

        location ~ \.php$ {
            fastcgi_pass   127.0.0.1:9000;
            fastcgi_index  index.php;
            fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
	        include        fastcgi_params;
        }

    }
```
重启nginx命令如下

```
sudo nginx -s reload
```

## 3.3 增加HOST记录
增加host记录,参考命令如下


```
sudo vim /etc/hosts
```
在里面增加配置项


```
127.0.0.1   autowebsafe.localhost
```

### 3.4 打开站点
接着去浏览器打开界面，URL地址如下

```
http://autowebsafe.localhost/
```
接着可以看到浏览器如下图所示，但创建容器的按钮并不可用，因为还缺少docker以及frp，因此还需要往后看。

![image](http://tuchuang.qsjianzhan.com/autowebsafe/1.png)


### 3.5 安装Docker

每个系统安装docker的方式各不一样，这里简单以Ubuntu和mac以及win举例吧

Ubuntu系统安装的参考命令如下(如不可用请自行百度搜索):

```
sudo apt-get install docker-engine
```
mac系统安装docker可以直接去官网下载安装包，参考地址如下
```
https://hub.docker.com/editions/community/docker-ce-desktop-mac?tab=description
```

windows 10系统安装docker可以直接去官网下载安装包，参考地址如下
```
https://hub.docker.com/editions/community/docker-ce-desktop-windows
```

安装好之后请确保处于启动状态

### 3.6 安装FRP

frp是一个内网穿透工具，这里主要是为了让项目可以外网使用，如果不需要外网使用则可以不用安装了，但内网地址则需要你通过代码中查看。

因为frp针对各种系统有不同的版本，因此需要读者自行去下载，下载的地址如下：

```
https://github.com/fatedier/frp/releases
```

选择自己系统对应的版本，笔者用的是mac电脑所以选择的是`frp_0.25.3_darwin_amd64.tar.gz`这个版本，如果是windows 64位的可以选择`frp_0.25.3_windows_amd64.zip`,其他系统以此为例。

下载并解压之后，可以看到里面有很多个文件，如下图所示

![image](http://tuchuang.qsjianzhan.com/autowebsafe/2.png)

我们只需要将frpc文件复制到我们刚才从GitHub下载下来的代码汇总即可，复制的位置是`autowebsafe/tools/frp/frpc`,其他就不用管了

至此安装部分已经完成了，接下来可以看演示部分

## 四、操作演示

### 4.1 创建容器
依然回到浏览器中，打开URL地址`http://autowebsafe.localhost/`,看到的界面如下

![image](http://tuchuang.qsjianzhan.com/autowebsafe/1.png)

然后点击`创建容器`按钮，浏览器等待3秒钟会自动刷新，刷新之后便可以看到如下列表

![image](http://tuchuang.qsjianzhan.com/autowebsafe/4.png)

### 4.2 安装靶场系统

在列表中有一个`打开网址`按钮，点击打开按钮，便可以进入安装页面，如下图所示

![image](http://tuchuang.qsjianzhan.com/autowebsafe/5.png)

安装好之后，可以进入首页，如下图所示

![image](http://tuchuang.qsjianzhan.com/autowebsafe/6.png)


### 五、其他

文档未完,待续...