import React, { useCallback, useState } from "react";
import styles from '../styles';
import { View, TextInput, TouchableOpacity } from 'react-native';
import { Entypo } from '@expo/vector-icons';
import { darkGray, lightPurple, white } from "../../../color";

export default function InputFieldsSignIn({setValue}) {
  const [whichFocusInputField, setWhichFocusInputField] = useState(null)
  const [stateLogin, setStateLogin] = useState([0, false])
  const [statePassword, setStatePassword] = useState([0, false])
  const [stateIconPassword, setStateIconPassword] = useState("eye-with-line")

  const onFocusInputFields = useCallback(function(nameInputField) {
    setWhichFocusInputField(nameInputField)
  }, [])

  const onBlurInputFields = useCallback(function(ltxt, nameInputField) {
    switch (nameInputField) {
      case "login":
        ltxt > 0 ? setStateLogin(p => [p[0], true]) : setStateLogin(p => [p[0], false])
        break;
      case "password":
        ltxt > 0 ? setStatePassword(p => [p[0], true]) : setStatePassword(p => [p[0], false])
        break;
      default:
        break;
    }
    setWhichFocusInputField(null)
  }, [])

  const seePassword = useCallback(function() {
    if (stateIconPassword === "eye")
      setStateIconPassword("eye-with-line")
    else
      setStateIconPassword("eye")
  }, [stateIconPassword])

  const onChangeField = useCallback(
    name => text => {
      setValue(name, text)
      switch (name) {
        case "login":
          setStateLogin(p => [text.length, p[1]])
          break;
        case "password":
          setStatePassword(p => [text.length, p[1]])
          break;
        default:
          break;
      }
    }, [])

  return (
    <View style={{width: '100%', alignItems: 'center', top: 30}}>
      <TextInput placeholder="Email address or Username" placeholderTextColor={whichFocusInputField === "login" ? white : darkGray}
        onFocus={() => onFocusInputFields("login")}
        onChangeText={onChangeField("login")}
        onBlur={() => onBlurInputFields(stateLogin[0], "login")}
        style={[styles.inputFields, whichFocusInputField === "login" || stateLogin[1] ? {borderBottomWidth: 0, borderRadius: 8, backgroundColor: lightPurple} : null]}/>
      <TextInput placeholder="Password" placeholderTextColor={whichFocusInputField === "password" ? white : darkGray}
        secureTextEntry={stateIconPassword === "eye" ? false : true}
        maxLength={13}
        onFocus={() => onFocusInputFields("password")}
        onChangeText={onChangeField("password")}
        onBlur={() => onBlurInputFields(statePassword[0], "password")}
        style={[styles.inputFields, whichFocusInputField === "password" || statePassword[1] ? {borderBottomWidth: 0, borderRadius: 8, backgroundColor: lightPurple} : null]}/>
      <TouchableOpacity
        activeOpacity={0.6}
        onPress={seePassword}
        style={{position: "absolute", top: 147, right: 45}}
      >
        <Entypo name={stateIconPassword} size={40} color={whichFocusInputField === "password" || statePassword[1] ? white : darkGray} />
      </TouchableOpacity>
    </View>
  )
}