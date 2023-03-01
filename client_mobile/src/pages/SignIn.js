import React, { useCallback, useEffect } from "react";
import styles from '../components/SignIn/styles';
import { Text, View, TouchableOpacity } from 'react-native';
import { useForm } from "react-hook-form";
import { useLogin } from "../api/apiSignPage";
import InputFieldsSignIn from "../components/SignIn/inputFields/index";
import { black, darkPurple, lightPurple, white } from "../color";
import AsyncStorage from "@react-native-async-storage/async-storage";

export default function SignIn({ navigation }) {
  const {register, handleSubmit, setValue} = useForm()
  const handleLogin = useLogin()

  const onSubmit = useCallback(formData => {
    for (const data in formData) {
      if (formData[data] === undefined || formData[data] === "") {
        alert("Please complete all the fields")
        return
      }
    }
    handleLogin.mutate(
      JSON.stringify({login: formData["login"], password: formData["password"]}),
      {
        onSuccess: () => {
          navigation.replace('Home')
        }
      }
    )
  }, [])

  useEffect(() => {
    AsyncStorage.getItem("token")
    .then((res) => {
      if (res !== null)
        navigation.replace('Home')
    })
    register("login")
    register("password")
  }, [register])

  return (
    <View style={styles.container}>
      <Text style={{fontSize: 45, fontWeight: 'bold', color: lightPurple, top: 50}}>Sign in</Text>
      <InputFieldsSignIn setValue={setValue}/>
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
      {handleLogin.isError ? <Text style={styles.ErrorMessage}>{handleLogin.error.response.data.message}</Text> : null}
    </View>
  );
}