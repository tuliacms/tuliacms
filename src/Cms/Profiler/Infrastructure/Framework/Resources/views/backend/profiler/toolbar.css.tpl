body.tulia-profiler-toolbar-opened {margin-bottom:38px;}

#profiler-toolbar {display:block;position:fixed;left:0;bottom:0;right:0;background-color:#111;color:#fff;font-size:13px;line-height:14px;z-index:2147483647;height:38px;font-family:Arial, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";}
#profiler-toolbar .text-info {color:#fff;line-height:38px;padding:0 15px;}
#profiler-toolbar.profiler-toolbar-hidden {left:auto;width:42px;border-radius:3px 0 0 0;}
#profiler-toolbar.profiler-toolbar-hidden .tulia-toolbar-block {display:none;}
.tulia-toolbar-icon {line-height:38px;height:38px;transition:.12s all;padding:0 10px;display:inline-block;white-space:nowrap;position:relative;}
.tulia-toolbar-icon:before {display:block;content:"";left:0;top:0;right:0;bottom:0;position:absolute;background-color:rgba(255,255,255,.2);transition:.12s all;opacity:0;pointer-events:none;}
.tulia-toolbar-icon svg {height:15px;display:inline-block;vertical-align:middle;margin-right:4px;opacity:.9;transition:.12s all;}
.tulia-toolbar-info {position:absolute;left:0;bottom:100%;background-color:#111;white-space:nowrap;display:none;}
.tulia-toolbar-block {display:inline-block;position:relative;vertical-align:middle;height:38px;}
.tulia-toolbar-block.tulia-toolbar-status-green .tulia-toolbar-icon {background-color:green;}
.tulia-toolbar-block.tulia-toolbar-status-yellow .tulia-toolbar-icon {background-color:#dc9800;}
.tulia-toolbar-block.tulia-toolbar-status-red .tulia-toolbar-icon {background-color:red;}
.tulia-toolbar-block .tulia-toolbar-status {display:inline-block;font-weight:bold;}
.tulia-toolbar-block .tulia-toolbar-status-green {background-color:green;}
.tulia-toolbar-block .tulia-toolbar-status-yellow {background-color:#dc9800;}
.tulia-toolbar-block .tulia-toolbar-status-red {background-color:red;}
.tulia-toolbar-block:hover .tulia-toolbar-icon:before {opacity:1;}
.tulia-toolbar-block:hover .tulia-toolbar-info {display:block;}
.tulia-toolbar-block:hover .tulia-toolbar-info svg {opacity:1;}
.tulia-toolbar-value {display:inline-block;vertical-align:middle;}
.tulia-toolbar-label {font-weight:bold;display:inline-block;vertical-align:middle;}
.tulia-toolbar-info-group {border-bottom:1px solid #333333;padding:5px 8px;}
.tulia-toolbar-info-piece {border-bottom:solid transparent 3px;display:table-row;}
.tulia-toolbar-info-piece b {color:#aaa;display:table-cell;font-size:11px;padding:4px 8px 4px 0;}
.tulia-toolbar-info-piece span {font-size:12px;}
.tulia-toolbar-info-piece a {color:#bbb;text-decoration:underline;}
.tulia-toolbar-info-piece a:hover {cursor:pointer;color:#fff;}
.tulia-toolbar-info-piece .tulia-toolbar-status-green,
.tulia-toolbar-info-piece .tulia-toolbar-status-yellow,
.tulia-toolbar-info-piece .tulia-toolbar-status-red {padding:2px 3px;border-radius:2px;}

.profiler-toolbar-close {position:absolute;top:0;right:0;bottom:0;z-index:10;line-height:36px;vertical-align:middle;padding:0 10px;transition:.12s all;}
.profiler-toolbar-close svg {width:20px;height:20px;display:inline-block;opacity:.9;transition:.12s all;}
.profiler-toolbar-close:hover {cursor:pointer;background-color:rgba(255,255,255,.15);}
.profiler-toolbar-close:hover svg {opacity:1}

.tulia-toolbar-block-request .tulia-toolbar-status {display:inline-block;padding:0 11px;font-weight:bold;transform:translateX(-10px)}
.tulia-toolbar-block-twig pre {overflow:auto;width:100%;height:200px;background-color:#fff;margin-bottom:0;padding:5px;}
