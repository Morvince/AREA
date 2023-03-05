import axios from "axios"
import { useMutation } from "react-query"
import AsyncStorage from '@react-native-async-storage/async-storage';

const getAutomation = async () => {
  const token = await AsyncStorage.getItem("token")
  return await axios.post("/automation/get", JSON.stringify({token: token}))
}

export const useGetAutomation = () => {
  return useMutation(getAutomation, {retry: 3})
}

const deleteAutomation = async (data) => {
  return await axios.post("/automation/delete", data)
}

export const useDeleteAutomation = () => {
  return useMutation(deleteAutomation, {retry: 3})
}