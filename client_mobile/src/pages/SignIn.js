import React, { useCallback, useEffect, useState } from "react";
import styles from '../components/SignIn/styles';
import { Text, View, TextInput, TouchableOpacity } from 'react-native';
import { Entypo } from '@expo/vector-icons';
import { useForm } from "react-hook-form";
import { darkGray, darkPurple, lightPurple, white } from "../color";
import Animated, { useAnimatedStyle, withTiming } from "react-native-reanimated";

export default function SignIn({ navigation }) {
  // const [slideForm, setSlideForm] = useState(0)
  // const bgColor = slideForm === 0 || slideForm === 2 ? white : black

  // const bgFade = useAnimatedStyle(() => {
  //   return {
    //     backgroundColor: withTiming(bgColor), //300ms duration by default
  //   }
  // })
  // const handleSlideForm = useCallback(function(event) {
    //   event.preventDefault()
    //   if (slideForm === 0)
    //     setSlideForm(s => s + 1)
  //   else if (slideForm === 1)
  //     setSlideForm(s => s + 1)
  //   else if (slideForm === 2)
  //     setSlideForm(s => s - 1)
  // }, [slideForm])
  const [whichFocusInputField, setWhichFocusInputField] = useState(null)
  const [stateLogin, setStateLogin] = useState([0, false])
  const [statePassword, setStatePassword] = useState([0, false])
  const [stateIconPassword, setStateIconPassword] = useState("eye-with-line")
  const {register, handleSubmit, setValue} = useForm()

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

  const onSubmit = useCallback(formData => {
    for (const data in formData) {
      if (formData[data] === undefined || formData[data] === "") {
        alert("Please complete all the fields")
        return
      }
    }
    navigation.navigate('Home')
  }, [])

  useEffect(() => {
    register("login")
    register("password")
  }, [register])

  return (
    <View style={styles.container}>
      <Text style={{fontSize: 45, fontWeight: 'bold', color: lightPurple, top: 50}}>Sign in</Text>
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
      <TouchableOpacity
        onPress={handleSubmit(onSubmit)}
        activeOpacity={0.8}
        style={styles.LogInButton}
      >
        <Text style={{fontSize: 25, color: white, fontWeight: 'bold'}}>Log in</Text>
      </TouchableOpacity>
      <TouchableOpacity
        activeOpacity={0.6}
        onPressIn={() => navigation.navigate('SignUp')}
      >
        <Text style={{fontSize: 16, color: darkPurple, textDecorationLine: 'underline', bottom: 50}}>Don't have an account?</Text>
      </TouchableOpacity>
    </View>
  );
}