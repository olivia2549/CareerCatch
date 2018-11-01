'use strict';

/**
 * generated by Progressive WordPress:
 * https://wordpress.org/plugins/progressive-wordpress/
 * by Nico Martin - https://nicomartin.ch
**/
const version = '1525488609';const offlinePage='';const staticCachePages=['/Playground/wordpress/','/Playground/wordpress/',''];const key='ProgressiveWordPress';const staticCacheName=`${key}-Static-${version}`;self.addEventListener('install',event=>{event.waitUntil(caches.open(staticCacheName).then(cache=>{return cache.addAll(staticCachePages)}).then(function(){return self.skipWaiting()}))});self.addEventListener('activate',event=>{event.waitUntil(caches.keys().then(keys=>{return Promise.all(keys.map(key=>{if(key!==staticCacheName){return caches.delete(key)}}))}))});self.addEventListener('fetch',event=>{let request=event.request;let url=new URL(request.url);if(url.origin!==location.origin){return}
if(request.url.match(/wp-admin/)||request.url.match(/preview=true/)){return}
if(request.method!=='GET'){event.respondWith(fetch(request).catch(error=>{return caches.match(offlinePage)}));return}
event.respondWith(fetch(request).then(response=>{addToCache(request);return response}).catch(error=>{return caches.match(request).then(response=>{return response||caches.match(offlinePage)})}));const addToCache=function(request){return caches.open(staticCacheName).then(cache=>{return fetch(request).then(response=>{return cache.put(request,response)})})}})