import { View, Text, TouchableOpacity, TextInput, Alert } from 'react-native';
import styles from '../styles';
import { Feather } from '@expo/vector-icons';
import { white } from '../../../color';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useEffect, useState } from 'react';

export default function MenuBar({stateMenuBar, handleSlideMenuBar, handleLogOut, handleChangeServerIp}) {
  const [serverIp, setServerIp] = useState(null)

  useEffect(() => {
    AsyncStorage.getItem("serverIp").then((res) => {
      setServerIp(res)
    })
  }, [])

  return (
    <View style={[styles.menuBar, stateMenuBar ? {width: '75%'} : null]}>
      <View style={{flex: 0.5, paddingRight: 3,  alignItems: 'center', justifyContent: 'center'}}>
        <TouchableOpacity activeOpacity={0.6} onPressOut={handleSlideMenuBar} style={stateMenuBar ? {position: 'absolute', left: 5, top: '3.5%'} : null}>
          <Feather name="menu" size={48} color={white}/>
        </TouchableOpacity>
        <Text style={{fontSize: 19, fontWeight: 'bold', color: white, textDecorationLine: 'underline'}}>Server IP Address:</Text>
        <TextInput defaultValue={serverIp} onSubmitEditing={handleChangeServerIp} style={styles.textInputServerIp}/>
        <TouchableOpacity activeOpacity={0.6} onPressOut={handleLogOut} style={styles.logOutButton}>
          <Text style={{fontSize: 20, fontWeight: 'bold', color: white, textAlign: 'center'}}>Log out</Text>
        </TouchableOpacity>
      </View>
    </View>
  )
}