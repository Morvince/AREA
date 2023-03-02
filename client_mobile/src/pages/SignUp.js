import React, { useCallback, useEffect } from "react";
import styles from '../components/SignUp/styles';
import { Text, View, TouchableOpacity } from 'react-native';
import { useForm } from "react-hook-form";
import { useRegister } from "../api/apiSignPage";
import InputFieldsSignUp from "../components/SignUp/inputFields";
import { black, darkPurple, lightPurple } from "../color";

export default function SignUp({ navigation }) {
  const {register, handleSubmit, setValue} = useForm()
  const handleRegister = useRegister()

  const onSubmit = useCallback(formData => {
    for (const data in formData) {
      if (formData[data] === undefined || formData[data] === "") {
        alert("Please complete all the fields")
        return
      }
    }
    if (formData.password !== formData.confirmpassword) {
      alert("The 2 passwords differed")
      return
    }
    if (!formData.email.includes("@") ||
        formData.email[0] === "@" ||
        formData.email[formData.email.length - 1] === "@") {
      alert("There is no valid Email Address")
      return
    }
    handleRegister.mutate(
      JSON.stringify({username: formData["username"], email: formData["email"], password: formData["password"]}),
      {
        onSuccess: () => {
          navigation.replace('Home')
        }
      }
    )
  }, [])

  useEffect(() => {
    register("username")
    register("email")
    register("password")
    register("confirmpassword")
  }, [register])

  return (
    <View style={styles.container}>
      <Text style={{fontSize: 45, fontWeight: 'bold', color: lightPurple, top: 50}}>Sign up</Text>
      <InputFieldsSignUp setValue={setValue}/>
      <TouchableOpacity
        onPress={handleSubmit(onSubmit)}
        activeOpacity={0.8}
        style={styles.SignUpButton}
      >
        <Text style={{fontSize: 25, color: black, fontWeight: 'bold'}}>Sign up</Text>
      </TouchableOpacity>
      <TouchableOpacity
        activeOpacity={0.6}
        onPressIn={() => navigation.navigate('SignIn')}
      >
        <Text style={{fontSize: 16, color: darkPurple, textDecorationLine: 'underline', bottom: 5, alignSelf: 'center'}}>Already have an account?</Text>
      </TouchableOpacity>
      {handleRegister.isError ? <Text style={styles.ErrorMessage}>{handleRegister.error.response.data.message}</Text> : null}
    </View>
  );
}