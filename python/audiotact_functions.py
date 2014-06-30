#!/usr/bin/python
# -*- coding: utf8 -*-

'''
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
'''

from __future__ import unicode_literals, division
import os, re, array, threading, time, gtk, gobject, socket, zipfile
import sqlite3 as lite
from pyudev import Context, Monitor, MonitorObserver
from threading import Thread
from zipfile import ZipFile
from collections import namedtuple

gobject.threads_init()
gtk.threads_init()

verbose = True
	
audiotact_dir = os.path.dirname(os.path.abspath(__file__)) #renvoi dossier Audiotact
_ntuple_diskusage = namedtuple('usage', 'total used free') #déclaration du tuple pour disk_usage


#Couleurs des boîtes de dialogue

win_color = "#6D6E71"
pb_color = "#BCBEC0"
pb_prelight = "#5B5B5B"
b_n_color = "#ebebeb"
b_prelight = "#5B5B5B"
font_color = "#464646"

def debug(message):
	"Print debug message if True"
	if verbose is True :
		print message


#Permet d'obtenir le chemin vers le dossier source 
def xdg_vars_reader(index):
	'''
	index		Path		index		Path
	---------------------------------------------
	0			Desktop		4			Documents
	1			Download	5			Music
	2			Templates	6			Images
	3			Public		7			Videos
	'''
	xdg_file = os.popen('echo -n ~/.config/user-dirs.dirs').read()
	f = open(xdg_file,"r")
	xdg_vars=[]
	home = os.popen('echo -n ~').read()
	for line in f:
		if re.match("XDG_(.*)",line):
			var_value = line.split("=")
			xdg_vars.append(re.sub('"','',var_value[1]))
	f.close()
	xdg_vars[index] = xdg_vars[index].replace("$HOME",home).replace("\n",'')
	return xdg_vars[index]+"/"

source_dir = xdg_vars_reader(1)

#Fonctions liées à la gestion SQLite
	
def audiotact_db_init():
	db = lite.connect(audiotact_dir+'/audiotact.db')
	cur = db.cursor()    
	cur.execute("CREATE TABLE IF NOT EXISTS target_device(device_id INTEGER PRIMARY KEY, device_path TEXT, device_label TEXT, device_mounted INT)")
	cur.execute("CREATE TABLE IF NOT EXISTS file_to_copy(file_id INTEGER PRIMARY KEY,filename TEXT)")
	cur.execute("DELETE FROM target_device")
	cur.execute("DELETE FROM file_to_copy")
	db.commit()
	db.close()
	
	debug("SQLite initialization	OK")

def create_device(device_path, device_label, device_mounted):
	debug("Create device %s %s %s" % (device_path, device_label, device_mounted)) 
	
	db = lite.connect(audiotact_dir+'/audiotact.db')
	cur = db.cursor()
	cur.execute('INSERT INTO target_device(device_path, device_label, device_mounted) VALUES(?,?,?)', (device_path, device_label, device_mounted))
	db.commit()
	db.close()
	
def update_device(device_id, device_mounted):
	debug("Update device %s %s" % (device_id, device_mounted))
	
	db = lite.connect(audiotact_dir+'/audiotact.db')
	cur = db.cursor()
	cur.execute('UPDATE target_device SET device_mounted = ? WHERE device_id = ?', (device_mounted, device_id))
	db.commit()
	db.close()
	
def delete_device(device_path):
	debug("Delete device %s" % device_path)
	
	db = lite.connect(audiotact_dir+'/audiotact.db')
	cur = db.cursor()
	cur.execute('DELETE FROM target_device WHERE device_path = ?', (device_path,))
	db.commit()
	db.close()	

def get_device_infos(device_id):
	debug("Get device infos %d " % device_id)
	
	device_id = str(device_id)
	db = lite.connect(audiotact_dir+'/audiotact.db')
	cur = db.cursor()
	cur.execute('SELECT * FROM target_device WHERE device_id = ?', (device_id))
	infos = cur.fetchone()
	db.close()
	return infos
	
def list_devices_infos():
	#debug("List device")
	
	db = lite.connect(audiotact_dir+'/audiotact.db')
	cur = db.cursor()
	cur.execute('SELECT * FROM target_device')
	infos = cur.fetchall()
	db.close()
	return infos

	#Gestion des fichiers à copier, dans SQLite
def add_file_to_copy(filename):
	debug("Create content to copy : %s" % filename)
	
	db = lite.connect(audiotact_dir+'/audiotact.db')
	cur = db.cursor()
	cur.execute('INSERT INTO file_to_copy(filename) VALUES(?)', (filename,))
	db.commit()
	db.close()	
	
def delete_file_to_copy(file_id):
	debug("Delete file with id : %d" % file_id)
	
	file_id = str(file_id)
	db = lite.connect(audiotact_dir+'/audiotact.db')
	cur = db.cursor()
	cur.execute('DELETE FROM file_to_copy WHERE file_id = ?', (file_id))
	db.commit()
	db.close()
		
def list_of_file_to_copy():
	#debug("List contents")
	
	db = lite.connect(audiotact_dir+'/audiotact.db')
	cur = db.cursor()
	cur.execute('SELECT * FROM file_to_copy')
	infos = cur.fetchall()
	db.close()
	return infos

def get_file_to_copy(filename):
	debug("Get content infos %s " % filename)

	db = lite.connect(audiotact_dir+'/audiotact.db')
	cur = db.cursor()
	cur.execute('SELECT * FROM file_to_copy WHERE filename = ?', [filename])
	infos = cur.fetchone()
	db.close()
	return infos
	
#Fonction relative à la gestion système de l'USB

	#Vérifie présence device usb au démarrage
def devices_at_start():
	context = Context()
	for device in context.list_devices(subsystem='block', DEVTYPE='partition'):
		if 'usb' in device.get('DEVPATH'):
			device_path = device.get('DEVNAME')
			device_label = device.get('ID_FS_LABEL_ENC')
			if device_label is  None:
				device_label = device.get('ID_FS_UUID')
			else:
				device_label = device_label.replace("\\x20"," ")
			if os.path.ismount("/media/"+device_label) is True:
				device_mounted = 1
				create_device(device_path,device_label,device_mounted)
			else:
				command = 'pmount %s "/media/%s"' % (device_path,device_label)
				os.system(command)
				device_mounted = 1
	debug(list_devices_infos())

	#Scrute les événements udev  
def devices_events(device):
	ui = UI()
	device_path = device.get('DEVNAME')
	
	device_label = device.get('ID_FS_LABEL_ENC')
	if device_label is  None:
		device_label = device.get('ID_FS_UUID')
	else:
		device_label = device_label.replace("\\x20"," ")
	if 'add' in device.action :
		time.sleep(0.5)
		if os.path.ismount("/media/"+device_label) is True:
			device_mounted = 1
		else:
			command = 'pmount %s "/media/%s"' % (device_path,device_label)
			os.system(command)
			device_mounted = 1
		create_device(device_path,device_label,device_mounted)
		ui.builder(device.action,device_label)
	elif 'remove' in device.action :
		delete_device(device_path)
		ui.builder(device.action,device_label)
		
	debug(list_devices_infos())

	#Calcule l'espace total, utilisé, et restant 
def disk_usage(path):
	st = os.statvfs(path)
	free = ((st.f_bavail * st.f_frsize)/1048576)
	total = ((st.f_blocks * st.f_frsize)/1048576)
	used = (((st.f_blocks - st.f_bfree) * st.f_frsize)/1048576)
	return _ntuple_diskusage(total, used, free)

	#Gestion du démontage des périphériques USB
def umount_device(device_label):
	if lock_builder is False:
		umount_ui = UI()
		nb = len(list_devices_infos())
		if nb == 1 and device_label == None:
			device_path = list_devices_infos()[0][1]
			device_label = list_devices_infos()[0][2]
			command = "pumount %s" % device_path
			target = "/media/%s" % device_label
			debug("umount %s %s" % (device_path,device_label))
			os.system(command)
			if verif_umount(target) is True :
				delete_device(device_path)				
				umount_ui.builder("umount", device_label)
		elif nb > 1  and device_label == None:
			target_id = umount_ui.builder("select_umount",None)
			device_path = get_device_infos(target_id)[1]
			device_label = get_device_infos(target_id)[2]
			command = "pumount %s" % device_path
			target = "/media/%s" % device_label
			debug("umount %s %s" % (device_path,device_label))
			os.system(command)
			if verif_umount(target) is True :
				delete_device(device_path)
				umount_ui.builder("umount", device_label)
		elif device_label != None:
			for index in range(len(list_devices_infos())):
				debug("Index %d" % index)
				debug(list_devices_infos())
				if list_devices_infos()[index][2] == device_label:
					device_path = list_devices_infos()[index][1]
					device_label = list_devices_infos()[index][2]
					command = "pumount %s" % device_path
					target = "/media/%s" % device_label
					debug("Commande %s " %  command)
					os.system(command)
					if verif_umount(target) is True :
						delete_device(device_path)
						umount_ui.builder("umount", device_label)
					break
		else :
			umount_ui.builder("no_device",None)


def verif_umount(target):
	if os.path.ismount(target) is not True:
		return True
	else:
		return False
		
#Gestion des fichiers à copier

file_size = None
file_to_copy = None
target_str = None
chunk_size = 102400
progression = 0
progressBar = None
update = None
copy = None
window = None
lock_builder = False
verif_copy = None
	
	#Classe copiant les fichiers par blocs de taille "chunk_size"
class copy_file(threading.Thread):
	stopthread = threading.Event()
	
	def run(self):
		global chunk_size
		global window
		global file_size
		global file_to_copy
		global target_str
		global progression
		global copy
		global lock_builder
		global verif_copy
		
		lock_builder = True	
		copied = 0
		while copied != file_size:
			try:
				chunk = file_to_copy.read(chunk_size)
				target_str.write(chunk)
				copied += len(chunk)
				progression = copied2percent(copied)
				time.sleep(0.005)
				verif_copy = True
			except IOError:
				remove_all_files()
				verif_copy = False
				break
		target_str.close()
		window.destroy()

	def stop(self):
		self.stopthread.set()
		
	#Centralise l'arrêt des threads progress bar + copy 
def main_quit(obj):
	global window
	global update
	global copy
	global lock_builder 	
	update.stop()
	copy.stop()
	gtk.main_quit() 
	lock_builder = False
	

	#Liste les fichiers à charger
def queue_manager():
	global lock_builder	
	incomplete_zip = ".zip.part"
	incomplete_mp3 = ".mp3.part"
	content = os.listdir(source_dir)
	if len(list_devices_infos()) > 0 and len(content) > 0:
		for index in range(len(content)):
			if incomplete_zip not in content[index] and incomplete_mp3 not in content[index] and get_file_to_copy(content[index]) is None and os.stat(source_dir+content[index]).st_size > 0 : 
				add_file_to_copy(content[index])
		for index in range(len(list_of_file_to_copy())):
			filename = get_file_to_copy(list_of_file_to_copy()[0][1])[1].decode("utf-8")
			if lock_builder is False:
				copy_manager(filename)
				if verif_copy is True:
					try:
						remove_file(filename)
					except TypeError:
						remove_all_files()
	elif len(content) > 0:
		no_copy = UI()
		no_copy.builder("no_device",None)
		remove_all_files()
		
	#Supervise la copie	
def copy_manager(filename):
	global target_str
	global file_to_copy
	global file_size
	if len(list_devices_infos()) > 0:
		file_size = os.path.getsize(source_dir+filename)
		file_to_copy = open(source_dir+filename,'rb')
		if len(list_devices_infos()) > 1:
			selectTarget = UI()
			target = get_device_infos(selectTarget.builder("select",None))[2]
			target_dir = "/media/%s/" % target
		else:
			target = list_devices_infos()[0][2]
			target_dir = "/media/%s/" % target
		free_space_on_device = disk_usage(target_dir)
		print "Place restante : %d " % free_space_on_device[2]
		testSize = free_space_on_device[2] - (file_size/1048576)
		print "Place restante après copie : %d Mo" % testSize
		if testSize > 0:
			try:
				target_str = open(target_dir+filename, 'wb')
				progressBarUI = UI()
				progressBarUI.builder("progress_bar", filename)
			except IOError:
				print "Problème d'écriture"
		else:
			debug("No more space available on %s" % target)
			no_spaceUI = UI()
			no_spaceUI.builder("no_space", target)
			umount_device(target)
			remove_file(filename)
			
	#Supprime un fichier et sa référence
def remove_file(filename):
	if len(os.listdir(source_dir)) > 0:
		os.system('rm "'+source_dir+filename+'"')
	delete_file_to_copy(get_file_to_copy(filename)[0])
	
	#Supprime tous les fichiers et leurs références
def remove_all_files():
	if len(os.listdir(source_dir)) > 0:
		os.system("rm %s*" % source_dir)	
	while len(list_of_file_to_copy()) > 0:
		delete_file_to_copy(list_of_file_to_copy()[0][0])

class get_files(Thread):
	def __init__(self):		
		Thread.__init__(self)
	def run(self):
		while 1:
			queue_manager()
			time.sleep(0.5)
		
#Classe d'écoute du socket
class socket_listener(Thread):
	def __init__(self):		
		ip = "127.0.0.1"
		port = 5005
		self.sock = socket.socket(socket.AF_INET,socket.SOCK_DGRAM)	
		self.sock.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR,1)  
		self.sock.bind((ip, port))
		self.stopped = False
		Thread.__init__(self)
	def run(self):
		while 1:
			self.event_catcher()
			time.sleep(0.5)
	def event_catcher(self):
		data, addr = self.sock.recvfrom(1024)
		if data == "umount":
			umount_device(None)
		elif data == "relaunch":
			print "Relaunch Audiotact"
			#os.system("./_audiotact_")
		elif data == "maintenance":
			print "Maintenance Mode"
			os.system("xterm ./_audiotact_maintenance.sh &")
		elif data == "reboot":
			print "Reboot Audiotact"
			os.system("sudo shutdown -r now")
		elif data == "halt":
			print "Halt Audiotact"
			os.system("sudo shutdown -h now")

#Classes et fonctions relatives à l'UI
def copied2percent(copied):
	percent = (100*(copied))/file_size
	return percent

class updateProgressBar(threading.Thread):
	stopthread = threading.Event()
	
	def run(self):
		global progressBar
		global progression
		global window
		
		while progression < 100:
			gtk.threads_enter()
			progressBar.set_fraction(progression/100)
			progressBar.set_text("Progression en cours - "+str(int(progression))+"%")
			gtk.threads_leave()
			time.sleep(0.01)
		
	def stop(self):
		self.stopthread.set()
		

class UI(object):
	"""UI class"""
	output = None	
	
	def __init__(self):
		window = None
					
	def builder(self, box_type, content):
		global lock_builder
		if lock_builder is False :
			#Constructeur de fenêtre
			nb = len(list_devices_infos())
			if box_type == "add":
				size = (450, 50)
				message = "%s est connecté"  % content
				timeout_enable = True
			elif box_type == "remove":
				size = (450, 50)
				message = "%s a été déconnecté"  % content 
				timeout_enable = True
			elif box_type == "umount":
				size = (450, 50)
				message = "%s peut être déconnecté"  % content 
				timeout_enable = True					
			elif box_type == "select_umount":
				height = 35*nb
				size = (450, 60+height)
				message = "Veuillez sélectionner le périphérique à déconnecter :"
				timeout_enable = False					
			elif box_type == "select" :
				height = 35*nb
				size = (450, 60+height)
				message = "Veuillez sélectionner une destination :"
				timeout_enable = False
			elif box_type == "no_device" :
				size = (450, 50)
				message = "Pas de périphérique branché"
				timeout_enable = True
			elif box_type == "no_space" :
				size = (450, 50)
				message = "Plus d'espace sur %s" % content
				timeout_enable = True				
			elif box_type == "progress_bar" :
				global progressBar
				global update
				global copy
				size = (600, 100)
				message = "Copie de %s en cours ..." % content
				timeout_enable = False
			else:
				size = (0,0)
				message = ""
				timeout_enable = True
			
			global window
			
			window = gtk.Window(type=gtk.WINDOW_POPUP)
			window.set_position(gtk.WIN_POS_CENTER)
			window.set_border_width(10)
			window.set_size_request(size[0],size[1])

			if message != None:
				vbox = gtk.VBox()
				window.add(vbox)
				display_message = gtk.Label("<b>"+message+"</b>")
				display_message.set_use_markup(True)
				display_message.modify_fg(gtk.STATE_NORMAL, gtk.gdk.color_parse(font_color))
				vbox.pack_start(display_message, False, False, 5)
				
			if timeout_enable is True:
				gtk.timeout_add(2000, self.timeout)
				
			if box_type == "progress_bar":
				window.connect('destroy', main_quit)
				progressBar = gtk.ProgressBar() #Création d'une barre de progression
				progressBar.set_text("Progression en cours - 0%") #Définir un texte pour la barre
				progressBar.modify_bg(gtk.STATE_NORMAL, gtk.gdk.color_parse(pb_color))
				progressBar.modify_bg(gtk.STATE_PRELIGHT, gtk.gdk.color_parse(pb_prelight))
				vbox.pack_start(progressBar, False, False, 10)	
				
			if box_type == "select" or box_type == "select_umount":
				buttonLabel = [None] * len(list_devices_infos())
				for index in range(len(list_devices_infos())):
					return_enable = True
					buttonLabel[index] = gtk.Button(list_devices_infos()[index][2],None)
					buttonLabel[index].modify_bg(gtk.STATE_NORMAL, gtk.gdk.color_parse(b_n_color))
					buttonLabel[index].modify_fg(gtk.STATE_NORMAL, gtk.gdk.color_parse(win_color))
					buttonLabel[index].modify_bg(gtk.STATE_PRELIGHT, gtk.gdk.color_parse(b_prelight))
					buttonLabel[index].connect("clicked", self.selected,index)
					vbox.pack_start(buttonLabel[index], False, False, 5)				
			else:
				return_enable = False
				
			window.show_all()
			
			if box_type == "progress_bar":
				update = updateProgressBar()
				copy = copy_file()
				copy.start()
				update.start()				
			
			gtk.main()
			
			if return_enable == True:
				return output
				
	def timeout(self):
		window.destroy()
		gtk.main_quit()
		return False
		
	def selected(self,button,index):
		global output
		output = list_devices_infos()[index][0]
		window.destroy()
		gtk.main_quit()

