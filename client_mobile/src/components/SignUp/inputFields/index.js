import React, { useCallback, useState } from "react";
import styles from '../styles';
import { View, TextInput, TouchableOpacity } from 'react-native';
import { Entypo } from '@expo/vector-icons';
import { darkGray, lightPurple, white } from "../../../color";

export default function InputFieldsSignUp({setValue}) {
  const [whichFocusInputField, setWhichFocusInputField] = useState(null)
  const [stateUsername, setStateUsername] = useState([0, false])
  const [stateEmail, setStateEmail] = useState([0, false])
  const [statePassword, setStatePassword] = useState([0, false])
  const [stateConfirmPassword, setStateConfirmPassword] = useState([0, false])
  const [stateIconPassword, setStateIconPassword] = useState("eye-with-line")
  const [stateIconConfirmPassword, setStateIconConfirmPassword] = useState("eye-with-line")

  const onFocusInputFields = useCallback(function(nameInputField) {
    setWhichFocusInputField(nameInputField)
  }, [])

  const onBlurInputFields = useCallback(function(ltxt, nameInputField) {
    switch (nameInputField) {
      case "email":
        ltxt > 0 ? setStateEmail(p => [p[0], true]) : setStateEmail(p => [p[0], false])
        break;
      case "username":
        ltxt > 0 ? setStateUsername(p => [p[0], true]) : setStateUsername(p => [p[0], false])
        break;
      case "password":
        ltxt > 0 ? setStatePassword(p => [p[0], true]) : setStatePassword(p => [p[0], false])
        break;
      case "confirmpassword":
        ltxt > 0 ? setStateConfirmPassword(p => [p[0], true]) : setStateConfirmPassword(p => [p[0], false])
        break;
      default:
        break;
    }
    setWhichFocusInputField(null)
  }, [])

  const seePassword = useCallback(function(whichIcon) {
    switch (whichIcon) {
      case "password":
        if (stateIconPassword === "eye")
          setStateIconPassword("eye-with-line")
        else
          setStateIconPassword("eye")
        break;
      case "confirmpassword":
        if (stateIconConfirmPassword === "eye")
          setStateIconConfirmPassword("eye-with-line")
        else
          setStateIconConfirmPassword("eye")
      default:
        break;
    }
  }, [stateIconPassword, stateIconConfirmPassword])

  const onChangeField = useCallback(
    name => text => {
      setValue(name, text)
      switch (name) {
        case "username":
          setStateUsername(p => [text.length, p[1]])
          break;
        case "email":
          setStateEmail(p => [text.length, p[1]])
          break;
        case "password":
          setStatePassword(p => [text.length, p[1]])
          break;
        case "confirmpassword":
          setStateConfirmPassword(p => [text.length, p[1]])
          break;
        default:
          break;
      }
    }, [])

  return (
    <View style={{width: '100%', alignItems: 'center', top: 30}}>
      <TextInput placeholder="Username" placeholderTextColor={whichFocusInputField === "username" ? white : darkGray}
        onFocus={() => onFocusInputFields("username")}
        onChangeText={onChangeField("username")}
        onBlur={() => onBlurInputFields(stateUsername[0], "username")}
        style={[styles.inputFields, whichFocusInputField === "username" || stateUsername[1] ? {borderBottomWidth: 0, borderRadius: 8, backgroundColor: lightPurple} : null]}/>
      <TextInput placeholder="Email address" placeholderTextColor={whichFocusInputField === "email" ? white : darkGray}
        inputMode="email"
        keyboardType="email-address"
        onFocus={() => onFocusInputFields("email")}
        onChangeText={onChangeField("email")}
        onBlur={() => onBlurInputFields(stateEmail[0], "email")}
        style={[styles.inputFields, whichFocusInputField === "email" || stateEmail[1] ? {borderBottomWidth: 0, borderRadius: 8, backgroundColor: lightPurple} : null]}/>
      <TextInput placeholder="Password" placeholderTextColor={whichFocusInputField === "password" ? white : darkGray}
        secureTextEntry={stateIconPassword === "eye" ? false : true}
        maxLength={13}
        onFocus={() => onFocusInputFields("password")}
        onChangeText={onChangeField("password")}
        onBlur={() => onBlurInputFields(statePassword[0], "password")}
        style={[styles.inputFields, whichFocusInputField === "password" || statePassword[1] ? {borderBottomWidth: 0, borderRadius: 8, backgroundColor: lightPurple} : null]}/>
      <TouchableOpacity
        activeOpacity={0.6}
        onPress={() => seePassword("password")}
        style={{position: "absolute", top: 232, right: 45}}
      >
        <Entypo name={stateIconPassword} size={40} color={whichFocusInputField === "password" || statePassword[1] ? white : darkGray} />
      </TouchableOpacity>
      <TextInput placeholder="Confirm your password" placeholderTextColor={whichFocusInputField === "confirmpassword" ? white : darkGray}
        secureTextEntry={stateIconConfirmPassword === "eye" ? false : true}
        maxLength={13}
        onFocus={() => onFocusInputFields("confirmpassword")}
        onChangeText={onChangeField("confirmpassword")}
        onBlur={() => onBlurInputFields(stateConfirmPassword[0], "confirmpassword")}
        style={[styles.inputFields, whichFocusInputField === "confirmpassword" || stateConfirmPassword[1] ? {borderBottomWidth: 0, borderRadius: 8, backgroundColor: lightPurple} : null]}/>
      <TouchableOpacity
        activeOpacity={0.6}
        onPress={() => seePassword("confirmpassword")}
        style={{position: "absolute", top: 332, right: 45}}
      >
        <Entypo name={stateIconConfirmPassword} size={40} color={whichFocusInputField === "confirmpassword" || stateConfirmPassword[1] ? white : darkGray} />
      </TouchableOpacity>
    </View>
  )
}