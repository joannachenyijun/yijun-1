"""
class SVNRepo:
    @classmethod
    def isBadVersion(cls, id)
        # Run unit tests to check whether verison `id` is a bad version
        # return true if unit tests passed else false.
You can use SVNRepo.isBadVersion(10) to check whether version 10 is a 
bad version.



The code base version is an integer start from 1 to n. One day, someone committed a bad version in the code case, so it caused this version and the following versions are all failed in the unit tests. Find the first bad version.

You can call isBadVersion to help you determine which version is the first bad one. The details interface can be found in the code's annotation part.

Example
Given n = 5:

isBadVersion(3) -> false
isBadVersion(5) -> true
isBadVersion(4) -> true
Here we are 100% sure that the 4th version is the first bad version.

Challenge
You should call isBadVersion as few as possible.
"""


class Solution:
    """
    @param: n: An integer
    @return: An integer which is the first bad version.
    """
    def findFirstBadVersion(self, n):
        # write your code here
        if n == 1:
            return 1
            
        index = 0
        
        if n > 2**32:
            while SVNRepo.isBadVersion(index) == False and index < n:
                index = 2*index + 1
        
            start = 0
            end = index
        else:
            start = 0
            end = n
            
        while start + 1 < end:
            mid = start+ ( end - start) // 2
            if SVNRepo.isBadVersion(mid) == False:
                start = mid
            else:
                end = mid
        
        if SVNRepo.isBadVersion(start) == True:
            return start
        elif SVNRepo.isBadVersion(end) == True:
            return end
        else:
            return mid
