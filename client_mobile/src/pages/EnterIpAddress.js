import AsyncStorage from '@react-native-async-storage/async-storage';
import { useCallback, useEffect } from 'react';
import { View, TextInput, Text, Alert } from 'react-native';
import axios from "axios"

export default function EnterIpAddress({navigation}) {
  useEffect(() => {
    AsyncStorage.getItem("serverIp")
    .then((res) => {
      if (res !== null)
        navigation.replace('SignIn')
    })
  }, [])

  const handleSubmitIp = useCallback(function(event) {
    if (event.nativeEvent.text !== null && event.nativeEvent.text !== "") {
      axios.defaults.baseURL = event.nativeEvent.text
      AsyncStorage.setItem("serverIp", axios.defaults.baseURL)
      .then((res) => {
        navigation.replace('SignIn')
      })
    } else
      Alert.alert("Missing Server IP", "Please enter the server IP Address")
  }, [])

  return (
    <View style={{flex: 1, alignItems: 'center', justifyContent: 'center'}}>
      <Text style={{fontSize: 28, fontWeight: 'bold', textAlign: 'center'}}>Please Enter the server</Text>
      <Text style={{fontSize: 28, fontWeight: 'bold', marginBottom: 25, textAlign: 'center'}}>IP Address</Text>
      <TextInput placeholder='Example: http(s)://my.server.ip:{port}'
        onSubmitEditing={handleSubmitIp}
        style={{fontSize: 20, borderWidth: 0.5, borderRadius: 10, paddingHorizontal: 10, paddingVertical: 18}}
      />
    </View>
  )
}